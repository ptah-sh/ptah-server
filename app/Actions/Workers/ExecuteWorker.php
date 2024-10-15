<?php

namespace App\Actions\Workers;

use App\Models\Backup;
use App\Models\BackupStatus;
use App\Models\DeploymentData\LaunchMode;
use App\Models\DeploymentData\Process;
use App\Models\DeploymentData\Worker;
use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\Service;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;

class ExecuteWorker
{
    use AsAction;

    public string $commandSignature = 'app:workers:execute {--service-id=} {--process=} {--worker=} {--backup-id=}';

    public string $commandDescription = 'Execute a worker';

    public function handle(Service $service, Process $process, Worker $worker, ?Backup $backup = null): void
    {
        DB::transaction(function () use ($service, $process, $worker, $backup) {
            $taskGroupType = match (true) {
                isset($worker->backupCreate) => NodeTaskGroupType::BackupCreate,
                isset($worker->backupRestore) => NodeTaskGroupType::BackupRestore,
                default => NodeTaskGroupType::ExecuteWorker,
            };

            $deployment = $service->latestDeployment;

            $taskGroup = NodeTaskGroup::create([
                'type' => $taskGroupType,
                'swarm_id' => $service->swarm_id,
                'node_id' => $process->placementNodeId,
                'invoker_id' => $deployment->latestTaskGroup->invoker_id,
                'team_id' => $service->team_id,
            ]);

            $backup = match ($worker->launchMode) {
                LaunchMode::BackupCreate => $this->createBackup($service, $process, $worker),
                LaunchMode::BackupRestore => $backup,
                default => null,
            };

            if ($backup) {
                $taskGroup->backups()->attach($backup);
            }

            $tasks = [];

            $tasks = [
                ...$tasks,
                ...$worker->asNodeTasks($deployment, $process, desiredReplicas: 1, backup: $backup),
            ];

            $taskGroup->tasks()->createMany($tasks);
        });
    }

    public function asController(ActionRequest $request, Service $service, string $process, string $worker): void
    {
        $backup = $request->input('backup') ? Backup::findOrFail($request->input('backup')) : null;

        [$service, $process, $worker] = $this->validate($service, $process, $worker);

        $this->handle($service, $process, $worker, $backup);
    }

    public function asCommand(Command $command)
    {

        $service = Service::findOrFail($command->option('service-id'));

        [$service, $process, $worker] = $this->validate($service, $command->option('process'), $command->option('worker'));

        $backup = $command->option('backup-id') ? Backup::findOrFail($command->option('backup-id')) : null;

        $this->handle($service, $process, $worker, $backup);
    }

    private function validate(Service $service, $process, $worker): array
    {
        $process = $service->latestDeployment->data->findProcess($process);
        if (! $process) {
            throw ValidationException::withMessages(['process' => 'Process not found']);
        }

        $worker = $process->findWorker($worker);
        if (! $worker) {
            throw ValidationException::withMessages(['worker' => 'Worker not found']);
        }

        if ($worker->launchMode === LaunchMode::Daemon) {
            throw ValidationException::withMessages(['worker' => 'Daemon workers cannot be executed']);
        }

        if ($process->placementNodeId) {
            Node::findOrFail($process->placementNodeId);
        }

        return [$service, $process, $worker];
    }

    private function createBackup(Service $service, Process $process, Worker $worker): Backup
    {
        $s3Storage = $service->swarm->data->findS3Storage($worker->backupCreate->s3StorageId);
        if ($s3Storage === null) {
            throw ValidationException::withMessages(['s3StorageId' => "Could not find S3 storage {$worker->backupCreate->s3StorageId} in swarm {$service->swarm_id}."]);
        }

        $date = now()->format('Y-m-d_His');

        $ext = $worker->backupCreate->archive->format->value;

        $backupFilePath = "/{$service->slug}/{$process->name}/{$worker->name}/{$service->slug}-{$process->name}-{$worker->name}-{$date}.{$ext}";

        // FIXME: what to do with backups which are "in progress" now for the same worker?
        $backup = new Backup;

        $backup->forceFill([
            'team_id' => $service->team_id,
            'service_id' => $service->id,
            'process' => $process->dockerName,
            'worker' => $worker->dockerName,
            's3_storage_id' => $s3Storage->id,
            'dest_path' => $backupFilePath,
            // FIXME: add "pending" status
            'status' => BackupStatus::InProgress,
            'started_at' => now(),
        ]);

        $backup->save();

        return $backup;
    }
}

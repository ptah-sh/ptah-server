<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTaskType;
use App\Models\Service;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DispatchProcessBackupTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backups:processes:create {--service-id=} {--process=} {--backup-cmd-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds a task to create Volume Backup on a Node.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $this->dispatchBackupTask();
        });
    }

    /**
     * @throws Exception
     */
    protected function dispatchBackupTask(): void
    {
        /* @var Service $service */
        $service = Service::findOrFail($this->option('service-id'));

        $deployment = $service->latestDeployment;

        $process = $deployment->data->findProcess($this->option('process'));
        if ($process === null) {
            throw new Exception("Could not find process {$this->option('process')} in deployment {$deployment->id}.");
        }

        $backupCmd = $process->findProcessBackup($this->option('backup-cmd-id'));
        if ($backupCmd === null) {
            throw new Exception("Could not find backup command {$this->option('backup-cmd-id')} in process {$process->name}.");
        }

        $node = Node::findOrFail($process->placementNodeId);

        $taskGroup = $node->taskGroups()->create([
            'swarm_id' => $node->swarm_id,
            'node_id' => $node->id,
            'type' => NodeTaskGroupType::CreateBackup,
            'invoker_id' => $deployment->latestTaskGroup->invoker_id,
            'team_id' => $service->team_id,
        ]);

        $date = now()->format('Y-m-d_His');
        $backupFilePath = "svc_{$service->id}/{$process->name}/cmd/{$backupCmd->name}/{$process->name}-cmd-{$backupCmd->name}-{$date}.tar.gz";
        $backupFileSlug = Str::slug($backupFilePath, separator: '_');
        $archivePath = "{$process->backupVolume->path}/$backupFileSlug";
        $backupCommand = "mkdir -p /tmp/{$backupCmd->id} && cd /tmp/{$backupCmd->id} && {$backupCmd->command} && tar czfv $archivePath -C /tmp/{$backupCmd->id} . && rm -rf /tmp/{$backupCmd->id}";

        $s3Storage = $node->swarm->data->findS3Storage($backupCmd->backupSchedule->s3StorageId);
        if ($s3Storage === null) {
            throw new Exception("Could not find S3 storage {$backupCmd->backupSchedule->s3StorageId} in swarm {$node->swarm_id}.");
        }

        $taskGroup->tasks()->createMany([
            [
                'type' => NodeTaskType::ServiceExec,
                'meta' => [
                    'serviceId' => $service->id,
                    'command' => $backupCommand,
                ],
                'payload' => [
                    'ProcessName' => $process->dockerName,
                    'ExecSpec' => [
                        'AttachStdout' => true,
                        'AttachStderr' => true,
                        'Cmd' => ['sh', '-c', $backupCommand],
                    ],
                ],
            ],
            [
                'type' => NodeTaskType::UploadS3File,
                'meta' => [
                    'serviceId' => $service->id,
                ],
                'payload' => [
                    'S3StorageConfigName' => $s3Storage->dockerName,
                    'VolumeSpec' => [
                        'Type' => 'volume',
                        'Source' => $process->backupVolume->dockerName,
                        'Target' => $process->backupVolume->path,
                    ],
                    'SrcFilePath' => $archivePath,
                    'DestFilePath' => $s3Storage->pathPrefix.'/'.$backupFilePath,
                ],
            ],
        ]);
    }
}

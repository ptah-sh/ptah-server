<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTaskType;
use App\Models\Scopes\TeamScope;
use App\Models\Service;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DispatchVolumeBackupTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backups:volumes:create {--service-id=} {--process=} {--volume-id=}';

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
        $service = Service::withoutGlobalScope(TeamScope::class)->with(['latestDeployment'])->findOrFail($this->option('service-id'));

        $deployment = $service->latestDeployment;

        $process = $deployment->data->findProcess($this->option('process'));
        if ($process === null) {
            throw new Exception("Could not find process {$this->option('process')} in deployment {$deployment->id}.");
        }

        $volume = $process->findVolume($this->option('volume-id'));
        if ($volume === null) {
            throw new Exception("Could not find volume {$this->option('volume-id')} in process {$process->name}.");
        }

        $node = Node::withoutGlobalScope(TeamScope::class)->findOrFail($process->placementNodeId);

        $taskGroup = $node->taskGroups()->create([
            'swarm_id' => $node->swarm_id,
            'node_id' => $node->id,
            'type' => NodeTaskGroupType::CreateBackup,
            'invoker_id' => $deployment->latestTaskGroup->invoker_id,
            'team_id' => $service->team_id,
        ]);

        $date = now()->format('Y-m-d_His');
        $backupFilePath = "svc_{$service->id}/{$process->name}/vol/{$volume->name}/{$process->name}-vol-{$volume->name}-{$date}.tar.gz";
        $backupFileSlug = Str::slug($backupFilePath, separator: '_');
        $archivePath = "{$process->backupVolume->path}/$backupFileSlug";
        $backupCommand = "tar czfv $archivePath -C {$volume->path} .";

        // TODO: get rid of copy-pasted code.
        $s3Storage = $node->swarm->data->findS3Storage($volume->backupSchedule->s3StorageId);
        if ($s3Storage === null) {
            throw new Exception("Could not find S3 storage {$volume->backupSchedule->s3StorageId} in swarm {$node->swarm_id}.");
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
                        'User' => 'root',
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

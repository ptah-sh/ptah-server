<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\Models\NodeTask;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\ServiceExec\ServiceExecMeta;
use App\Models\NodeTaskType;
use App\Models\Service;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DispatchBackupTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-backup-task {--service-id=} {--process=} {--volume=}';

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

        $volume = $process->findVolume($this->option('volume'));
        if ($volume === null) {
            throw new Exception("Could not find volume {$this->option('volume')} in process {$process->name}.");
        }

        $node = Node::findOrFail($deployment->data->placementNodeId);

        $taskGroup = $node->taskGroups()->create([
            'swarm_id' => $node->swarm_id,
            'node_id' => $node->id,
            'type' => NodeTaskGroupType::CreateBackup,
            'invoker_id' => $deployment->latestTaskGroup->invoker_id,
            'team_id' => $service->team_id,
        ]);

        $date = now()->format('Y-m-d_His');
        $backupFileName = dockerize_name("svc-{$service->id}-{$process->name}-{$volume->name}-{$date}") . '.tar.gz';
        $archivePath = "{$process->backupVolume->path}/$backupFileName";
        $backupCommand = "tar czfv $archivePath -C {$volume->path} .";

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
                        'AttachStdout' => true,
                        'AttachStderr' => true,
                        'Cmd' => ['sh', '-c', $backupCommand],
                    ]
                ]
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
                    'DestFilePath' => $s3Storage->pathPrefix . '/' . $backupFileName,
                ],
            ]
        ]);
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTaskType;
use App\Models\Service;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExecuteScheduledWorker extends Command
{
    protected $signature = 'app:workers:execute {--service-id=} {--process=} {--worker=}';

    protected $description = 'Execute a scheduled worker';

    public function handle()
    {
        DB::transaction(function () {
            $this->executeWorker();
        });
    }

    protected function executeWorker(): void
    {
        /* @var Service $service */
        $service = Service::findOrFail($this->option('service-id'));

        $deployment = $service->latestDeployment;

        $process = $deployment->data->findProcess($this->option('process'));
        if ($process === null) {
            throw new Exception("Could not find process {$this->option('process')} in deployment {$deployment->id}.");
        }

        $worker = $process->findWorker($this->option('worker'));
        if ($worker === null) {
            throw new Exception("Could not find worker {$this->option('worker')} in process {$process->name}.");
        }

        $node = $process->placementNodeId ? Node::findOrFail($process->placementNodeId) : null;

        $taskGroup = NodeTaskGroup::create([
            'type' => NodeTaskGroupType::LaunchService,
            'swarm_id' => $service->swarm_id,
            'node_id' => $node->id,
            'invoker_id' => $deployment->latestTaskGroup->invoker_id,
            'team_id' => $service->team_id,
        ]);

        $tasks = [];

        $tasks = [
            ...$tasks,
            ...$worker->asNodeTasks($deployment, $process, desiredReplicas: 1),
        ];

        if ($worker->backupOptions) {
            $s3Storage = $node->swarm->data->findS3Storage($worker->backupOptions->s3StorageId);
            if ($s3Storage === null) {
                throw new Exception("Could not find S3 storage {$worker->backupOptions->s3StorageId} in swarm {$node->swarm_id}.");
            }

            $archiveFormat = $worker->backupOptions->archive?->format->value;

            $date = now()->format('Y-m-d_His');

            $ext = $archiveFormat ? ".$archiveFormat" : '';
            $backupFilePath = "/{$service->slug}/{$process->name}/{$worker->name}/{$service->slug}-{$process->name}-{$worker->name}-{$date}$ext";

            $tasks[] = [
                'type' => NodeTaskType::UploadS3File,
                'meta' => [
                    'serviceId' => $service->id,
                    'destPath' => $backupFilePath,
                ],
                'payload' => [
                    'Archive' => [
                        'Enabled' => $worker->backupOptions->archive !== null,
                        'Format' => $archiveFormat,
                    ],
                    'S3StorageConfigName' => $s3Storage->dockerName,
                    'VolumeSpec' => [
                        'Type' => 'volume',
                        'Source' => $worker->backupOptions->backupVolume->dockerName,
                        'Target' => $worker->backupOptions->backupVolume->path,
                    ],
                    'SrcFilePath' => $worker->backupOptions->backupVolume->path,
                    'DestFilePath' => $backupFilePath,
                    'RemoveSrcFile' => true,
                ],
            ];
        }

        $taskGroup->tasks()->createMany($tasks);
    }
}

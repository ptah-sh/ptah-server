<?php

namespace App\Console\Commands;

use App\Models\Backup;
use App\Models\BackupStatus;
use App\Models\Node;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\UploadS3File\UploadS3FileMeta;
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

        $taskGroupType = $worker->backupCreate
            ? NodeTaskGroupType::BackupCreate
            : NodeTaskGroupType::ExecuteScheduledWorker;

        $taskGroup = NodeTaskGroup::create([
            'type' => $taskGroupType,
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

        if ($worker->backupCreate) {
            $s3Storage = $node->swarm->data->findS3Storage($worker->backupCreate->s3StorageId);
            if ($s3Storage === null) {
                throw new Exception("Could not find S3 storage {$worker->backupCreate->s3StorageId} in swarm {$node->swarm_id}.");
            }

            $archiveFormat = $worker->backupCreate->archive?->format->value;

            $date = now()->format('Y-m-d_His');

            $ext = $archiveFormat ? ".$archiveFormat" : '';
            $backupFilePath = "/{$service->slug}/{$process->name}/{$worker->name}/{$service->slug}-{$process->name}-{$worker->name}-{$date}{$ext}";

            $tasks[] = [
                'type' => NodeTaskType::UploadS3File,
                'meta' => UploadS3FileMeta::validateAndCreate([
                    'serviceId' => $service->id,
                    'destPath' => $backupFilePath,
                ]),
                'payload' => [
                    'Archive' => [
                        'Enabled' => $worker->backupCreate->archive !== null,
                        'Format' => $archiveFormat,
                    ],
                    'S3StorageConfigName' => $s3Storage->dockerName,
                    'VolumeSpec' => [
                        'Type' => 'volume',
                        'Source' => $worker->backupCreate->backupVolume->dockerName,
                        'Target' => $worker->backupCreate->backupVolume->path,
                    ],
                    'SrcFilePath' => $worker->backupCreate->backupVolume->path,
                    'DestFilePath' => $backupFilePath,
                    'RemoveSrcFile' => true,
                ],
            ];

            // FIXME: what to do with backups which are "in progress" now for the same worker?
            $backup = new Backup;

            $backup->forceFill([
                'team_id' => $service->team_id,
                'task_group_id' => $taskGroup->id,
                'service_id' => $service->id,
                'process' => $process->name,
                'worker' => $worker->name,
                's3_storage_id' => $s3Storage->id,
                'dest_path' => $backupFilePath,
                'status' => BackupStatus::InProgress,
                'started_at' => now(),
            ]);

            $backup->save();
        }

        $taskGroup->tasks()->createMany($tasks);
    }
}

<?php

namespace App\Actions\Workers;

use App\Models\Backup;
use App\Models\NodeTaskGroup;
use App\Models\NodeTaskGroupType;
use App\Models\NodeTasks\RemoveS3File\RemoveS3FileMeta;
use App\Models\NodeTaskType;
use App\Models\Service;
use App\Models\Team;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsCommand;

class RemoveStaleBackups implements ShouldQueue
{
    use AsCommand, Queueable;

    public string $commandSignature = 'app:backups:remove-stale {--team=}';

    public function handle(?Team $team): void
    {
        $teams = $team?->id ? collect([$team]) : Team::all();

        $teams->each(function (Team $team) {
            $team->services()->each(function (Service $service) use ($team) {
                $service->backups()->where('created_at', '<', now()->subDays($team->backup_retention_days))->chunk(20, function (Collection $backups) use ($service) {
                    $tasks = $backups->map(function (Backup $backup) use ($service) {
                        $s3Storage = $service->swarm->data->findS3Storage($backup->s3_storage_id);
                        if (! $s3Storage) {
                            Log::warning('S3Storage not found for backup', ['backup_id' => $backup->id]);

                            return;
                        }

                        return [
                            'type' => NodeTaskType::RemoveS3File,
                            'meta' => RemoveS3FileMeta::validateAndCreate([
                                's3StorageId' => $s3Storage->id,
                                'filePath' => $backup->dest_path,
                                'backupId' => $backup->id,
                            ]),
                            'payload' => [
                                'S3StorageConfigName' => $s3Storage->dockerName,
                                'FilePath' => $backup->dest_path,
                            ],
                        ];
                    });

                    if (empty($tasks)) {
                        return;
                    }

                    $taskGroup = NodeTaskGroup::create([
                        'type' => NodeTaskGroupType::BackupRemove,
                        'node_id' => null,
                        'team_id' => $service->team_id,
                        'swarm_id' => $service->swarm_id,
                        'invoker_id' => $service->latestDeployment->latestTaskGroup->invoker_id,
                    ]);

                    $taskGroup->tasks()->createMany($tasks);
                });
            });
        });
    }

    public function asCommand($command)
    {
        $team = Team::findOrFail($command->option('team'));

        $this->handle($team);
    }
}

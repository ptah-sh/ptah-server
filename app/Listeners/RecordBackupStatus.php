<?php

namespace App\Listeners;

use App\Events\NodeTaskGroups\BackupCreate\BackupCreateCompleted;
use App\Events\NodeTaskGroups\BackupCreate\BackupCreateFailed;
use App\Events\NodeTasks\RemoveS3File\RemoveS3FileCompleted;
use App\Models\Backup;
use App\Models\BackupStatus;
use Illuminate\Events\Dispatcher;

class RecordBackupStatus
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    public function subscribe(Dispatcher $dispatcher): array
    {
        return [
            BackupCreateCompleted::class => 'handleCompleted',
            BackupCreateFailed::class => 'handleFailed',
            RemoveS3FileCompleted::class => 'handleRemoveS3FileCompleted',
        ];
    }

    /**
     * Handle the event.
     */
    public function handleCompleted(BackupCreateCompleted $event): void
    {
        Backup::where('task_group_id', $event->taskGroup->id)->update(['status' => BackupStatus::Succeeded, 'ended_at' => now()]);
    }

    public function handleFailed(BackupCreateFailed $event): void
    {
        Backup::where('task_group_id', $event->taskGroup->id)->update(['status' => BackupStatus::Failed, 'ended_at' => now()]);
    }

    public function handleRemoveS3FileCompleted(RemoveS3FileCompleted $event): void
    {
        if (! $event->task->meta->backupId) {
            return;
        }

        Backup::where('id', $event->task->meta->backupId)->delete();
    }
}

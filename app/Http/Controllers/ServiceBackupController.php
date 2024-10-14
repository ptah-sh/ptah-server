<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Models\DeploymentData\LaunchMode;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class ServiceBackupController extends Controller
{
    public function index(Service $service): Response
    {
        $backups = Backup::where('service_id', $service->id)->latest()->paginate();
        $s3Storages = $service->swarm->data->s3Storages;

        $restoreWorkers = collect($service->latestDeployment->data->processes)
            ->flatMap(fn ($process) => $process->workers)
            ->filter(fn ($worker) => $worker->launchMode === LaunchMode::BackupRestore)
            ->map(fn ($worker) => [
                'name' => $worker->name,
                'command' => $worker->command,
                'dockerName' => $worker->dockerName,
            ])
            ->values();

        return Inertia::render('Services/Backups', [
            'service' => $service,
            'backups' => $backups,
            's3Storages' => $s3Storages,
            'restoreWorkers' => $restoreWorkers,
        ]);
    }
}

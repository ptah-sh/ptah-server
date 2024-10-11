<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class ServiceBackupController extends Controller
{
    public function index(Service $service): Response
    {
        $backups = Backup::where('service_id', $service->id)->latest()->paginate();
        $s3Storages = $service->swarm->data->s3Storages;

        return Inertia::render('Services/Backups', ['service' => $service, 'backups' => $backups, 's3Storages' => $s3Storages]);
    }
}

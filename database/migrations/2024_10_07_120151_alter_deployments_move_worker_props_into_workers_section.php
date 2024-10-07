<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::query()->from('deployments')->orderBy('id', 'asc')->chunk(100, function ($deployments) {
            foreach ($deployments as $deployment) {
                $data = json_decode($deployment->data);

                $healthcheck = new stdClass;
                $healthcheck->command = null;

                $releaseCommand = new stdClass;
                $releaseCommand->command = null;

                foreach ($data->processes as $process) {
                    foreach ($process->workers as $worker) {
                        $worker->dockerRegistryId = $process->dockerRegistryId;
                        $worker->dockerImage = $process->dockerImage;
                        $worker->launchMode = $process->launchMode;
                        $worker->replicas = $process->replicas;
                        $worker->releaseCommand = $releaseCommand;
                        $worker->healthcheck = $healthcheck;
                    }

                    $process->workers = [
                        [
                            'name' => 'main',
                            'dockerName' => $process->dockerName.'_'.'wkr_main',
                            'dockerRegistryId' => $process->dockerRegistryId,
                            'dockerImage' => $process->dockerImage,
                            'launchMode' => $process->launchMode,
                            'replicas' => $process->replicas,
                            'releaseCommand' => $process->releaseCommand,
                            'healthcheck' => $process->healthcheck,
                            'command' => $process->command,
                        ],
                        ...$process->workers,
                    ];
                }

                DB::query()->from('deployments')->where('id', $deployment->id)->update([
                    'data' => json_encode($data),
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::query()->from('deployments')->orderBy('id', 'asc')->chunk(100, function ($deployments) {
            foreach ($deployments as $deployment) {
                $data = json_decode($deployment->data);

                foreach ($data->processes as $process) {
                    $process->workers = array_slice($process->workers, 1);
                }

                DB::query()->from('deployments')->where('id', $deployment->id)->update([
                    'data' => json_encode($data),
                ]);
            }
        });
    }
};

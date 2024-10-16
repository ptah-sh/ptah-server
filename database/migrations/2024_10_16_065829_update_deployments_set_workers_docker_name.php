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

                foreach ($data->processes as $process) {
                    foreach ($process->workers as $worker) {
                        if (! $worker->dockerName) {
                            $worker->dockerName = $process->dockerName;
                        }
                    }
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
        //
    }
};

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
        DB::table('deployments')->select('id', 'data')->orderBy('id')->chunk(20, function ($deployments) {
            foreach ($deployments as $deployment) {
                $data = json_decode($deployment->data, true);

                foreach ($data['processes'] as &$process) {
                    foreach ($process['workers'] as &$worker) {
                        $worker['source'] = [
                            'type' => 'docker_image',
                            'docker' => [
                                'registryId' => $worker['dockerRegistryId'],
                                'image' => $worker['dockerImage'],
                            ],
                        ];
                    }
                }

                DB::table('deployments')->where('id', $deployment->id)->update(['data' => json_encode($data)]);
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

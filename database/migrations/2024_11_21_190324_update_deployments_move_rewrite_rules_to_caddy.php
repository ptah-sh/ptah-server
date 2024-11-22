<?php

use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('deployments')->select('id', 'data')->orderBy('id')->chunk(100, function ($deployments) {
            foreach ($deployments as $deployment) {
                $data = Json::decode($deployment->data);
                foreach ($data['processes'] as &$process) {
                    foreach ($process['caddy'] as &$caddy) {
                        $caddy['rewriteRules'] = $process['rewriteRules'];
                        $caddy['redirectRules'] = $process['redirectRules'];
                    }
                }

                DB::table('deployments')->where('id', $deployment->id)->update(['data' => $data]);
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

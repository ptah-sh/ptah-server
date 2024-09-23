<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->json('quotas_override')->default(json_encode([
                'nodes' => 0,
                'swarms' => 0,
                'services' => 0,
                'deployments' => 0,
            ]));
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('quotas_override');
        });
    }
};

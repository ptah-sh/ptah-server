<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // Change the column type to jsonb
            $table->jsonb('quotas_override')->default(json_encode([
                'nodes' => 0,
                'swarms' => 0,
                'services' => 0,
                'deployments' => 0,
            ]))->change();
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            // Revert the column type back to json
            $table->json('quotas_override')->default(json_encode([
                'nodes' => 0,
                'swarms' => 0,
                'services' => 0,
                'deployments' => 0,
            ]))->change();
        });
    }
};

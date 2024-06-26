<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('node_tasks', function (Blueprint $table) {
            $table->foreignId('meta__deployment_id')
                ->nullable()
                ->storedAs('("meta"->\'deploymentId\')::int')
                ->constrained('deployments')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('node_tasks', function (Blueprint $table) {
            $table->dropColumn('meta__deployment_id');
        });
    }
};

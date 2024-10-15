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
        Schema::create('backup_node_task_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('backup_id')->constrained('backups')->onDelete('cascade');
            $table->foreignId('node_task_group_id')->constrained('node_task_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('backup_node_task_group');
    }
};

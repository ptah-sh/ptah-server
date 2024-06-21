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
        Schema::create('node_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_group_id')->constrained('node_task_groups')->cascadeOnDelete();
            $table->smallInteger('type');
            $table->jsonb('meta');
            $table->jsonb('payload');
            $table->enum('status', ['pending', 'running', 'completed', 'failed', 'canceled'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->jsonb('result')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('node_tasks');
    }
};

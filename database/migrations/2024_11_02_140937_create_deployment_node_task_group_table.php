<?php

use App\Models\NodeTaskGroupType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deployment_node_task_group', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deployment_id')->constrained('deployments');
            $table->foreignId('node_task_group_id')->constrained('node_task_groups');
        });

        $query = <<<'SQL'
        INSERT INTO deployment_node_task_group (deployment_id, node_task_group_id)
        SELECT DISTINCT meta__deployment_id, ntg.id
        FROM node_tasks nt
        INNER JOIN node_task_groups ntg ON ntg.id = nt.task_group_id 
        WHERE ntg.type IN (?, ?, ?) AND meta__deployment_id IS NOT NULL
        ORDER BY ntg.id
        SQL;

        DB::insert($query, [NodeTaskGroupType::LaunchService->value, NodeTaskGroupType::CreateService->value, NodeTaskGroupType::UpdateService->value]);

        Schema::table('node_tasks', function (Blueprint $table) {
            $table->dropColumn('meta__deployment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('deployment_node_task_group');
    }
};

<?php

use App\Models\NodeTaskGroupType;
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
        Schema::table('deployments', function (Blueprint $table) {
            $table->foreignId('configured_by_id')->constrained('users');
        });

        $query = <<<'SQL'
        UPDATE deployments d
        SET configured_by_id = ntg.invoker_id
        FROM deployment_node_task_group dntg
        INNER JOIN node_task_groups ntg ON ntg.id = dntg.node_task_group_id
        WHERE dntg.deployment_id = d.id AND ntg.type IN (?, ?, ?)
        SQL;

        DB::update($query, [NodeTaskGroupType::LaunchService->value, NodeTaskGroupType::CreateService->value, NodeTaskGroupType::UpdateService->value]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn('configured_by_id');
        });
    }
};

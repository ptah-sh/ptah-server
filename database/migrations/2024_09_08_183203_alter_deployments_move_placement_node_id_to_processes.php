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
        DB::statement("
            UPDATE deployments
            SET data = jsonb_set(
                data,
                '{processes}',
                (
                    SELECT jsonb_agg(
                        process || jsonb_build_object('placementNodeId', data->'placementNodeId')
                    )
                    FROM jsonb_array_elements(data -> 'processes') AS process
                )
            ) - 'placementNodeId'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            UPDATE deployments
            SET data = jsonb_set(
                data,
                '{placementNodeId}',
                (data -> 'processes' -> 0 -> 'placementNodeId')
            ) || jsonb_set(
                data,
                '{processes}',
                (
                    SELECT jsonb_agg(process - 'placementNodeId')
                    FROM jsonb_array_elements(data -> 'processes') AS process
                )
            )
        ");
    }
};

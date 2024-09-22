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
                        jsonb_set(
                            process,
                            '{secretVars}',
                            COALESCE(process->'secretVars'->'vars', '[]'::jsonb)
                        ) - 'secretVars' || jsonb_build_object('secretVars', process->'secretVars'->'vars')
                    )
                    FROM jsonb_array_elements(data->'processes') AS process
                )
            )
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
                '{processes}',
                (
                    SELECT jsonb_agg(
                        jsonb_set(
                            process,
                            '{secretVars}',
                            jsonb_build_object('vars', process->'secretVars')
                        )
                    )
                    FROM jsonb_array_elements(data->'processes') AS process
                )
            )
        ");
    }
};

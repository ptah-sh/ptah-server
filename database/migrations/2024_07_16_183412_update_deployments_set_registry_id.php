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
        DB::update("
            WITH registries as (
                SELECT
                    r ->> 'dockerName' AS docker_name,
                    r ->> 'id' AS registry_id
                FROM
                    swarms,
                    jsonb_array_elements(swarms.data -> 'registries') r
            )
            UPDATE
                deployments
            SET
                data = jsonb_set(
                    data,
                    '{processes}',
                    (
                        SELECT
                            jsonb_agg(
                                process || jsonb_build_object(
                                    'dockerRegistryId',
                                    (
                                        SELECT
                                            registry_id FROM registries
                                        WHERE
                                            registries.docker_name = process->>'dockerRegistry'
                                    )
                                )
                            )
                        FROM jsonb_array_elements(data -> 'processes') AS process	
                    )
                )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

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
                        process || '{
                            \"healthcheck\": {
                                \"command\": null,
                                \"interval\": 10,
                                \"timeout\": 5,
                                \"retries\": 10,
                                \"startPeriod\": 60,
                                \"startInterval\": 10
                            }
                        }'::jsonb
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
        DB::statement("
            UPDATE deployments
            SET data = jsonb_set(
                data,
                '{processes}',
                (
                    SELECT jsonb_agg(process - 'healthcheck')
                    FROM jsonb_array_elements(data -> 'processes') AS process
                )
            )
        ");
    }
};

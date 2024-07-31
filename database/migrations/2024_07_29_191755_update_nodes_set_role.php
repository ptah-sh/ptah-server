<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::update("
            UPDATE nodes
            SET data = jsonb_set(
              data,
              '{role}',
              '\"manager\"'::jsonb
            )
            WHERE data IS NOT NULL;
        ");

        DB::update("
            UPDATE swarms
            SET data = jsonb_set(
              data,
              '{joinTokens}',
              '{\"manager\":\"\",\"worker\":\"\"}'::jsonb
            );
        ");

        DB::update("
            UPDATE swarms
            SET data = jsonb_set(
              data,
              '{managerNodes}',
              '[]'::jsonb
            );
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

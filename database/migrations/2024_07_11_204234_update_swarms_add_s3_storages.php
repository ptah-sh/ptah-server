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
            UPDATE swarms
            SET data = jsonb_set(
              data,
              '{s3Storages}',
              '[]'::jsonb
            );
        ");

        DB::update("
            UPDATE swarms
            SET data = jsonb_set(
              data,
              '{s3StoragesRev}',
              '0'::jsonb
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

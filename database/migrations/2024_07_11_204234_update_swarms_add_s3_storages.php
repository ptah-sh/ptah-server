<?php

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

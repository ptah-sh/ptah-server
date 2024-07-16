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
            UPDATE deployments
            SET data = jsonb_set(
              data,
              '{processes}',
              (
                SELECT jsonb_agg(process || jsonb_build_object('backups', '[]'::jsonb))
                FROM jsonb_array_elements(data->'processes') AS process
              )
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

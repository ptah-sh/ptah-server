<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateNodeDataAddress extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            UPDATE nodes
            SET data = data || '{\"address\": \"10.0.0.2\"}'::jsonb
            WHERE (data->>'address') IS NULL OR (data->>'address') = ''
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            UPDATE nodes
            SET data = data - 'address'
            WHERE data ? 'address'
        ");
    }
}

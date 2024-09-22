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
            UPDATE swarms
            SET data = data || '{\"encryptionKey\": \"-\"}'::jsonb
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            UPDATE swarms
            SET data = data - 'encryptionKey'
        ");
    }
};

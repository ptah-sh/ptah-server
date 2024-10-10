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
        DB::update("UPDATE node_tasks SET result = '{\"output\":[\"Output not available\"]}' WHERE type = 18 AND status = 'completed'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

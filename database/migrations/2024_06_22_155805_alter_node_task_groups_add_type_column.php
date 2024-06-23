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
        Schema::table('node_task_groups', function (Blueprint $table) {
            $table->smallInteger('type')->nullable();
        });

        DB::update('UPDATE node_task_groups SET type = 0');

        Schema::table('node_task_groups', function (Blueprint $table) {
            $table->smallInteger('type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('node_task_groups', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};

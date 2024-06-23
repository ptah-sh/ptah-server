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
        Schema::table('networks', function (Blueprint $table) {
            $table->integer('swarm_id')->nullable();
        });

        DB::update('UPDATE networks SET swarm_id = (SELECT id FROM swarms LIMIT 1)');

        Schema::table('networks', function (Blueprint $table) {
            $table->foreignId('swarm_id')->change()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('networks', function (Blueprint $table) {
            $table->dropColumn('swarm_id');
        });
    }
};

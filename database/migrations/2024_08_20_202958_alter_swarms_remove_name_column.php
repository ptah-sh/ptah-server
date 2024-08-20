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
        Schema::table('swarms', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('swarms', function (Blueprint $table) {
            $table->string('name')->after('id');
        });

        DB::table('swarms')->update(['name' => DB::raw('CAST(id AS VARCHAR)')]);

        Schema::table('swarms', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
        });
    }
};

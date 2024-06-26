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
        Schema::table('services', function (Blueprint $table) {
            $table->string('docker_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::update("UPDATE services SET docker_name = '<auto set on rollback>' WHERE docker_name IS NULL");

        Schema::table('services', function (Blueprint $table) {
            $table->string('docker_name')->nullable(false)->change();
        });
    }
};

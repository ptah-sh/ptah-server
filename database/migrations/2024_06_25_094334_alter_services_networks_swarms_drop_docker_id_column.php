<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('docker_id');
        });

        Schema::table('swarms', function (Blueprint $table) {
            $table->dropColumn('docker_id');
        });

        Schema::table('networks', function (Blueprint $table) {
            $table->dropColumn('docker_id');
        });

        Schema::table('nodes', function (Blueprint $table) {
            $table->dropColumn('docker_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('docker_id')->nullable();
        });

        Schema::table('swarms', function (Blueprint $table) {
            $table->string('docker_id')->nullable();
        });

        Schema::table('networks', function (Blueprint $table) {
            $table->string('docker_id')->nullable();
        });

        Schema::table('nodes', function (Blueprint $table) {
            $table->string('docker_id')->nullable();
        });
    }
};

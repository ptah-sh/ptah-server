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
        Schema::table('node_tasks', function (Blueprint $table) {
            $table->string('meta__docker_name')
                ->nullable()
                ->storedAs('("meta"->>\'dockerName\')::text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('node_tasks', function (Blueprint $table) {
            $table->dropColumn('meta__docker_name');
        });
    }
};

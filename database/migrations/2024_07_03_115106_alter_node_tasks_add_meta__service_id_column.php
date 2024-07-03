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
            $table->foreignId('meta__service_id')
                ->nullable()
                ->storedAs('("meta"->\'serviceId\')::int')
                ->constrained('services')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('node_tasks', function (Blueprint $table) {
            $table->dropColumn('meta__service_id');
        });
    }
};

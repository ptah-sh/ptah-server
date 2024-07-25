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
        Schema::table('teams', function (Blueprint $table) {
            $table->string('billing_name')->nullable();
            $table->string('billing_email')->nullable();
        });

        DB::update('UPDATE teams SET billing_name = users.name FROM users WHERE teams.user_id = users.id');
        DB::update('UPDATE teams SET billing_email = users.email FROM users WHERE teams.user_id = users.id');

        Schema::table('teams', function (Blueprint $table) {
            $table->string('billing_name')->change();
            $table->string('billing_email')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('billing_name');
            $table->dropColumn('billing_email');
        });
    }
};

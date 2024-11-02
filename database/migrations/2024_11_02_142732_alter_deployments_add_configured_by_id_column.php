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
        Schema::table('deployments', function (Blueprint $table) {
            $table->foreignId('configured_by_id')->constrained('users');
        });

        $query = <<<'SQL'
        UPDATE deployments d
        SET configured_by_id = t.user_id
        FROM teams t
        WHERE t.id = d.team_id
        SQL;

        DB::update($query);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            $table->dropColumn('configured_by_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlugToServicesTable extends Migration
{
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
        });

        // Fill the slug column with the slugified name
        DB::table('services')->whereNull('slug')->update([
            'slug' => DB::raw("LOWER(REPLACE(name, ' ', '-'))"),
        ]);

        // Make the slug column unique
        Schema::table('services', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}

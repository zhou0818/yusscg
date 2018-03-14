<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInfoFillInfoToNewStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('new_students', function (Blueprint $table) {
            $table->json('info')->nullable();
            $table->json('fill_info')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('new_students', function (Blueprint $table) {
            $table->dropColumn('info');
            $table->dropColumn('fill_info');
        });
    }
}

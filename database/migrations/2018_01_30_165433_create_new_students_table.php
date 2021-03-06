<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('new_students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('reg_num')->unique();
            $table->string('password');
            $table->string('weixin_openid')->unique()->nullable();
            $table->string('weixin_unionid')->unique()->nullable();
            //是否补填
            $table->boolean('is_fill')->default(false);
            //是否确认
            $table->boolean('is_confirm')->default(false);
            //是否摇中
            $table->boolean('is_lottery')->default(false);
            //是否录取
            $table->boolean('is_admit')->default(false);
            $table->string('admit_remark')->nullable();
            $table->string('class_remark')->nullable();
            $table->rememberToken();
            $table->timestamps();
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
            Schema::dropIfExists('new_students');
        });
    }
}

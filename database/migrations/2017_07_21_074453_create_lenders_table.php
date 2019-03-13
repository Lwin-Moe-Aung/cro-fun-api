<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLendersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lenders', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('code_no');
            $table->string('nrc');
            $table->dateTime('dob')->nullable();
            $table->string('state');
            $table->string('township');
            $table->string('phone_no');
            $table->text('address');
            $table->string('photo')->nullable();
            $table->string('attachment');
            $table->string('gender');
            $table->boolean('verified');
            $table->timestamps();
            $table->timestamps('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lenders');
    }
}

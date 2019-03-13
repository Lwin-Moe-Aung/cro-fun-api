<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFieldOfficersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_officers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('code_no',255);
            $table->string('nrc',255);
            $table->dateTime('dob')->nullable();
            $table->string('state');
            $table->string('township');
            $table->string('phone_no');
            $table->string('address',255);
            $table->string('photo',255)->nullable();
            $table->string('attachment');
            $table->string('gender');
            $table->integer('admin_id');
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
        Schema::dropIfExists('field_officers');
    }
}

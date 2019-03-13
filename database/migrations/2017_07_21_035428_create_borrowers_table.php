<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBorrowersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('borrowers', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('code_no');
            $table->string('nrc');
            $table->datetime('dob')->nullable();
            $table->text('address');
            $table->string('photo')->nullable();
            $table->string('state');
            $table->string('township');
            $table->string('phone_no');
            $table->string('attachment');
            $table->string('gender');
            $table->string('points');
            $table->integer('field_officers_id')->unsigned();
            $table->foreign('field_officers_id')->references('id')->on('field_officers');

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
        Schema::table('borrowers', function(Blueprint $table)
    {

        $table->dropForeign('borrowers_field_officers_id_foreign');
        $table->dropColumn('field_officers_id');

    });
        
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvestmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('project_id')->unsigned();
            $table->foreign('project_id')->references('id')->on('projects');
            $table->integer('lender_id')->unsigned();
            $table->foreign('lender_id')->references('id')->on('lenders');
            $table->dateTime('investment_date');
            $table->decimal('amount',10,2);
            $table->decimal('profit_estimation',10,2);
            $table->decimal('profit_percentage',10,2);
            $table->decimal('display_amount',10,2);
            $table->string('transaction_no',255);
            $table->string('investment_type',255);
            $table->text('investment_details');
            $table->string('status',255);
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
        Schema::dropIfExists('investments');
    }
}

<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProfitDistributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profit_distributions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('profit_id')->unsigned();
            $table->foreign('profit_id')->references('id')->on('profits');
            $table->integer('lender_id')->unsigned();
            $table->foreign('lender_id')->references('id')->on('lenders');
            $table->decimal('profit',10,2);
            $table->decimal('revenue',10,2);
            $table->dateTime('profit_paid_date')->nullable();
            $table->string('status',255);
            $table->string('transaction_no',255);
            $table->decimal('profit_distribution_percentage',10,2);
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
        Schema::dropIfExists('profit_distributions');
    }
}

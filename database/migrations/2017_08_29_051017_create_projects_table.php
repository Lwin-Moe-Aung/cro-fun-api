<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('borrower_id')->unsigned();
            $table->foreign('borrower_id')->references('id')->on('borrowers');
            $table->integer('field_officers_id')->unsigned();
            $table->foreign('field_officers_id')->references('id')->on('field_officers');
            $table->string('code_no');
            $table->string('project_title');
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->decimal('loan_value',10,2);
            $table->decimal('return_estimation_proposed',10,2);
            $table->decimal('return_estimation_approved',10,2)->default('0');
            $table->decimal('profit_sharing_estimation',10,2)->default('0');
            $table->text('profit_sharing_description')->nullable();
            $table->string('project_risk',255)->nullable();
            $table->string('project_grade',255)->nullable();
            $table->decimal('minimum_investment_amount',10,2);
            $table->integer('collateral_availability');
            $table->decimal('collateral_estimated_value',10,2);
            $table->text('collateral_description');
            $table->string('collateral_evidence');
            $table->integer('project_period');
            $table->string('state');
            $table->string('township');
            $table->text('project_location');
            $table->string('project_image');
            $table->text('project_description');
            $table->dateTime('fund_closing_date');
            $table->dateTime('project_start_date');
            $table->dateTime('project_end_date');
            $table->string('status');
            $table->boolean('featured');
            $table->text('comment')->nullable();
            $table->text('commodity');
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
        Schema::dropIfExists('projects');
    }
}

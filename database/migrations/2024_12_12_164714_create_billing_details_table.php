<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_details', function (Blueprint $table) {
            $table->increments('id'); // UNSIGNED INT
            $table->unsignedInteger('bill_id'); // Matches `id` in `billings`
            $table->string('to_number', 100);
            $table->string('from_cli', 100);
            $table->string('simplified_to_descriptive', 100);
            $table->integer('total_duration')->comment('seconds');
            $table->string('total_amount', 10);
            $table->string('currency', 5);
            $table->timestamps(0);
        
            // Add foreign key constraint to bill_id
            $table->foreign('bill_id')->references('id')->on('billings')->onDelete('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_details');
    }
}

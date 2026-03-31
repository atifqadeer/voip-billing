<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalBillingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_billing_details', function (Blueprint $table) {
            $table->increments('id'); // UNSIGNED INT
            $table->unsignedBigInteger('bill_id'); // Use unsignedBigInteger if `billings.id` is BIGINT
            $table->unsignedBigInteger('additional_service_id'); // Ensure this matches the type of `additional_services.id`
            $table->string('frequency', 10);
            $table->string('description', 255)->nullable();
            $table->tinyInteger('quantity')->default(1)->nullable(); // Default value for quantity
            $table->string('rate', 10)->default('0.00'); // Default rate value
            $table->string('total', 10)->default('0.00'); // Default total value
            $table->date('start_from')->nullable();
            $table->date('end_to')->nullable();
            $table->string('currency', 5)->default('USD'); // Default currency
            $table->timestamps(0);
            
            // Foreign key to `billings` table
            $table->foreign('bill_id')
                ->references('id')
                ->on('billings'); // Optional: Add cascade delete if needed
        
            // Foreign key to `additional_services` table
            $table->foreign('additional_service_id')
                ->references('id')
                ->on('additional_services'); // Optional: Add cascade delete if needed
            
            // Indexes for foreign key columns
            $table->index('bill_id');
            $table->index('additional_service_id');
        });
        
        
        
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('additional_billing_details');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxBillingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_billing_details', function (Blueprint $table) {
            $table->increments('id');  // Auto-increment primary key
            $table->unsignedInteger('bill_id');  // Foreign key referencing billings table
            $table->string('tax_type', 20);  // Tax type (e.g., VAT, Sales Tax, etc.)
            $table->unsignedInteger('tax_id');  // Foreign key referencing taxes table
            $table->string('tax_name', 50);  // Tax name
            $table->decimal('tax_rate', 5, 2);  // Tax rate (percentage)
            $table->string('tax_amount', 20);  // Total tax amount
            $table->string('currency', 5);  // Currency code (e.g., USD, EUR)
            $table->timestamps(0);  // created_at and updated_at timestamps

            // Foreign key constraints
            $table->foreign('bill_id')->references('id')->on('billings');
            $table->foreign('tax_id')->references('id')->on('taxes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_billing_details');
    }
}

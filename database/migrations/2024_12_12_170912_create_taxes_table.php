<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('taxes', function (Blueprint $table) {
            $table->increments('id');  // Auto-increment primary key
            $table->unsignedBigInteger('user_id');  // Foreign key to users table, unsigned integer
            $table->string('name', 50);  // Tax name (e.g., VAT, GST, Sales Tax)
            $table->string('rate', 10);  // Tax rate
            $table->enum('type', ['fixed', 'percentage']);  // Type of tax (fixed or percentage)
            $table->string('currency', 20)->nullable();  // Currency for the tax (nullable)
            $table->enum('status', ['enable', 'disable'])->default('enable');  // Tax status
            $table->timestamps(0);  // created_at and updated_at timestamps

            // Adding foreign key constraint for user_id
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxes');
    }
}

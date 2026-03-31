<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('client_id');
            $table->string('year', 5);
            $table->string('month', 5);
            $table->integer('total_duration')->comment('seconds');
            $table->double('total_payment', 8, 2);
            $table->string('currency', 20);
            $table->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');
            $table->string('pdf_file_name', 255)->nullable();
            $table->timestamps(0);  // created_at and updated_at

            // Add foreign key constraint to client_id
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billings');
    }
}

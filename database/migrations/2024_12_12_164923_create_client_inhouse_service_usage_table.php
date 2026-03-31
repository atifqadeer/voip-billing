<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientInhouseServiceUsageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_inhouse_service_usage', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedInteger('additional_service_id');
            $table->date('start_from')->nullable();
            $table->date('end_to')->nullable();
            $table->tinyInteger('quantity')->nullable();
            $table->string('rate', 5)->nullable();
            $table->text('description')->nullable();
            $table->timestamps(0);  // created_at and updated_at

            // Foreign key constraint to clients table
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            // Foreign key constraint to additional_services table
            $table->foreign('additional_service_id')->references('id')->on('additional_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_inhouse_service_usage');
    }
}

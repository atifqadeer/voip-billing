<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientServiceChargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_service_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('client_service_usage_id');
            $table->integer('duration')->comment('Duration in minutes');
            $table->integer('rate')->comment('Rate per unit');
            $table->timestamps(0);  // created_at and updated_at

            // Foreign key constraint to users table
            $table->foreign('user_id')->references('id')->on('users');

            // Foreign key constraint to clients table
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            // Foreign key constraint to client_service_usage table
            $table->foreign('client_service_usage_id')->references('id')->on('client_service_usage')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_service_charges');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('additional_services', function (Blueprint $table) {
            $table->increments('id'); // UNSIGNED INT
            $table->string('title', 255);
            $table->string('description', 255)->nullable();
            $table->string('rate', 10);
            $table->string('frequency', 10);
            $table->enum('status', ['enable', 'disable'])->default('enable');
            $table->unsignedBigInteger('user_id');
            $table->string('currency', 5);
            $table->timestamps(0);
        
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
        Schema::dropIfExists('additional_services');
    }
}

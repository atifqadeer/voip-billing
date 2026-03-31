<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCdrsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cdrs', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('reference', 200);
            $table->string('trunk', 100);
            $table->string('tag', 20)->nullable();
            $table->string('from_cli', 50)->nullable();
            $table->string('from_descriptive', 20)->nullable();
            $table->string('to_number', 50)->nullable();
            $table->string('to_descriptive', 100)->nullable();
            $table->string('simplified_to_descriptive', 100)->nullable();
            $table->integer('destination_id');
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->integer('billable_duration_seconds');
            $table->integer('peak_duration')->default(0);
            $table->integer('off_peak_duration')->default(0);
            $table->string('peak_rate', 10)->default('0');
            $table->string('off_peak_rate', 10)->default('0');
            $table->string('weekend_rate', 10)->default('0');
            $table->integer('weekend_duration')->default(0);
            $table->string('connection_rate', 10)->default('0');
            $table->string('total_charge', 10)->default('0');
            $table->string('currency', 5);
            $table->string('bill_amount', 20)->default('0');
            $table->time('calculated_duration');
            $table->timestamps(0);  // automatically handles created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cdrs');
    }
}

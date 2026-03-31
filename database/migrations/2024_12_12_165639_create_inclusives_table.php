<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInclusivesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inclusives', function (Blueprint $table) {
            $table->increments('id');  // Auto-increment primary key
            $table->unsignedInteger('inhouse_service_id');  // Foreign key column
            $table->string('skip_to', 100);  // Skip to (up to 100 characters)
            $table->enum('status', ['enable', 'disable'])->default('enable');  // Status field
            $table->timestamps(0);  // created_at and updated_at timestamps
            
            // Adding foreign key constraint
            $table->foreign('inhouse_service_id')->references('id')->on('additional_services')
                  ->onDelete('cascade');  // You can change 'cascade' to 'restrict' or 'set null' based on your needs
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inclusives');
    }
}

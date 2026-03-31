<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDescriptiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('descriptive', function (Blueprint $table) {
            $table->increments('id');  // Auto-increment primary key
            $table->string('description_name', 100);  // Description name (e.g., type of service)
            $table->text('replace_with')->nullable();  // Replace with (can be null)
            $table->enum('status', ['enable', 'disable'])->default('enable');  // Status field
            $table->timestamps(0);  // created_at and updated_at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('descriptive');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrencyListTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currency_list', function (Blueprint $table) {
            $table->increments('id');  // Auto-increment primary key
            $table->string('code', 5);  // Currency code (e.g., USD, EUR)
            $table->string('name', 255);  // Full name of the currency (e.g., US Dollar, Euro)
            $table->string('symbol', 5);  // Currency symbol (e.g., $, €, ¥)
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
        Schema::dropIfExists('currency_list');
    }
}

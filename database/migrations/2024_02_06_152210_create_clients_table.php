<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('account_number', 50);
            $table->string('client_name');
            $table->string('client_email')->nullable();
            $table->string('client_phone_number')->nullable();
            $table->string('client_outgoing_number')->nullable();
            $table->string('client_incoming_number')->nullable();
            $table->string('client_address')->nullable();
            $table->string('tag_name', 100)->nullable();
            $table->string('trunk_number', 100)->nullable();
            $table->enum('frequency', ['monthly', 'quarterly', 'annually'])->default('monthly');
            $table->text('notes')->nullable();
            $table->enum('status', ['enable', 'disable'])->default('enable');
            $table->enum('in_enable_vat_tax', ['0', '1'])->default('1');
            $table->enum('is_enable_fixed_line_services', ['0', '1'])->default('0');
            $table->enum('is_deleted', ['0', '1'])->default('0');
            $table->date('added_date');
            $table->time('added_time');
            $table->timestamps();

            // Add foreign key constraint for user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}

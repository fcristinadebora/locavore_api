<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('street');
            $table->string('number')->length(20)->nullable();
            $table->string('district')->length(50)->nullable();
            $table->string('city')->length(100)->nullable();
            $table->string('state')->length(50)->nullable();
            $table->string('country')->length(20)->nullable();
            $table->string('complement')->length(100)->nullable();
            $table->decimal('lat', 20,14)->length(100)->nullable();
            $table->decimal('long', 20,14)->length(100)->nullable();
            $table->string('name')->length(50)->nullable();
            $table->string('postal_code')->length(20)->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();
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
        Schema::dropIfExists('addresses');
    }
}

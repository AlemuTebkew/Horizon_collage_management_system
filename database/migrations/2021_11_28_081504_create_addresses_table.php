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
            $table->string('country',15)->comment('for non ethiopian')->nullable();
            $table->string('region',15)->nullable();
            $table->string('zone',15)->nullable();
            $table->string('subcity',15)->nullable();
            $table->string('kebele',15)->nullable();
            $table->string('house_no',15)->nullable();
            $table->string('town',15)->nullable();
            $table->timestamps();
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

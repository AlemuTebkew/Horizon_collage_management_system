<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcademicFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('academic_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_type_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('amount');

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
        Schema::dropIfExists('academic_fees');
    }
}

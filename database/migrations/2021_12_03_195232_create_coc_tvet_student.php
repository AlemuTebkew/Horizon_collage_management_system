<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCocTvetStudent extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coc_tvet_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tvet_student_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('coc_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->date('application_date')->nullable();
            $table->double('result')->default(0.0);
            $table->string('nature_of_assesment')->nullable();
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
        Schema::dropIfExists('coc_tvet_student');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentCocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_cocs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('coc_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->date('application_date');
            $table->double('result');
            $table->string('nature_of_assesment');

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
        Schema::dropIfExists('student_cocs');
    }
}

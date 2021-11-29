<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDegreeStudentSemestersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('degree_student_semesters', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->foreignId('degree_student_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('semester_GPA');
            $table->string('tution_type');
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
        Schema::dropIfExists('degree_student_semesters');
    }
}

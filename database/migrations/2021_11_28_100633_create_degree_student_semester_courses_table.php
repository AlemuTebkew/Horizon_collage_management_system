<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDegreeStudentSemesterCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('degree_student_semester_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('degree_student_semester_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('total_mark');
            $table->string('grade_point');
            $table->text('remark');

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
        Schema::dropIfExists('degree_student_semester_courses');
    }
}

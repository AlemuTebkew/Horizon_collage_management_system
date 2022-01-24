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
        Schema::create('student_semester_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('degree_student_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('from_5')->default(0);
            $table->double('from_5s')->default(0);
            $table->double('from_25')->default(0);
            $table->double('from_25s')->default(0);
            $table->double('from_40')->default(0);
            $table->double('total_mark')->default(0);
            $table->double('grade_point')->default(0);
            $table->string('letter_grade')->nullable();
            $table->text('remark')->nullable();

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

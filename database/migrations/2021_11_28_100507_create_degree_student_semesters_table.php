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
        Schema::create('degree_student_semester', function (Blueprint $table) {
            $table->id();
            $table->foreignId('degree_student_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('year_no');
            $table->string('semester_no');
            $table->double('semester_GPA')->default(0.0);
            $table->integer('semester_credit_hour')->nullable();
            $table->double('monthly_cp_fee')->default(0.0);
            $table->double('semester_grade_point')->default(0.0);
            $table->double('semester_average')->default(0.0);
            $table->double('cgpa')->default(0.0);
            $table->string('tuition_type')->nullable();
            // is he finishe expected fees for teacher enter result
            $table->boolean('legible')->default(0)->comment('is he finishe expected fees for teacher enter result');
            $table->string('status')->nullable();
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

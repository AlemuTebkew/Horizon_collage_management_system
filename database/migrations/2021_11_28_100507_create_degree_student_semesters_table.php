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
            $table->double('semester_GPA')->default(4.0);
            $table->integer('semester_credit_hour')->nullable();
            $table->double('monthly_cp_fee')->nullable();
            $table->double('semester_grade_point')->nullable();
            $table->double('semester_average')->nullable();
            $table->double('cgpa')->nullable();
            $table->string('tuition_type')->nullable();
            // $table->boolean('partial_scholarship')->default(0);
            //$table->boolean('approved')->default(0);
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

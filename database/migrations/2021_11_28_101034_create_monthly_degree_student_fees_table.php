<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonthlyDegreeStudentFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('degree_student_month', function (Blueprint $table) {
            $table->id();
          //  $table->foreignId('semester_month_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('month_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('degree_student_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('academic_fee_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('paid_amount')->nullable();
            $table->dateTime('paid_date');
            $table->double('receipt_no');
            $table->boolean('is_paid');
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
        Schema::dropIfExists('monthly_degree_student_fees');
    }
}

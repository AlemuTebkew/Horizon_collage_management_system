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
            $table->foreignId('month_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('degree_student_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('fee_type_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('academic_year_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('paid_amount')->default(0);
            $table->date('paid_date')->nullable();
            $table->string('receipt_no')->nullable();
            $table->string('payable_status')->default('payable');
            $table->boolean('is_paid')->default(0);
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

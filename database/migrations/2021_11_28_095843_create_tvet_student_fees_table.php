<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTvetStudentFeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tvet_student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tvet_student_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('academic_fee_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('month_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('paid_amount');
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
        Schema::dropIfExists('tvet_student_fees');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemesterPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semester_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('degree_semester_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('degree_student_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('academic_fee_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('receipt_no');
            $table->double('amount');
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
        Schema::dropIfExists('semester_payments');
    }
}

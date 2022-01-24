<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTvetOtherFees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tvet_other_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tvet_student_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('fee_type_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('receipt_no')->nullable();
            $table->double('paid_amount')->default(0.0);
            $table->date('paid_date')->nullable();
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
        Schema::dropIfExists('tvet_other_fees');
    }
}

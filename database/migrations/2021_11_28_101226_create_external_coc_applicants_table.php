<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExternalCocApplicantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('external_coc_applicants', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('sex');
            $table->date('dob');
            $table->string('phone_no');
            $table->string('marital_status');


            $table->foreignId('coc_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->date('application_date');
            $table->double('result');
            $table->string('nature_of_assesment');

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
        Schema::dropIfExists('external_coc_applicants');
    }
}

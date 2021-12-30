<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTvetStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tvet_students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique()->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('sex');
            $table->date('dob');
            $table->string('phone_no')->nullable();
            $table->string('residence_tel')->nullable();
            $table->string('residence_office_tel')->nullable();
            $table->string('maritial_status')->nullable();
            $table->string('contact_full_name')->nullable();
            $table->string('contact_relationship')->nullable();
            $table->string('contact_tel')->nullable();
            $table->string('contact_phone_no')->nullable();
            $table->string('contact_office_tel')->nullable();
            $table->string('EGSSE_result')->nullable();
            $table->string('EHEEE_result')->nullable();

                 //foreign keys from address table
            $table->foreignId('birth_address_id')->constrained('addresses','id')->cascadeOnDelete()->cascadeOnUpdate();//place_of_birth_address
            $table->foreignId('residential_address_id')->constrained('addresses','id')->cascadeOnDelete()->cascadeOnUpdate();//current_residential_address
            $table->foreignId('emergency_address_id')->constrained('addresses','id')->cascadeOnDelete()->cascadeOnUpdate();//emergency_contact_address

            $table->foreignId('program_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('tvet_department_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->string('financial_source')->nullable();
            $table->string('no_of_level')->nullable();
            $table->string('current_level_no')->nullable();
            $table->string('batch');
            $table->boolean('fully_scholarship')->default(0);
            $table->boolean('is_graduated')->default(0);
            $table->date('graduated_date')->nullable();


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
        Schema::dropIfExists('tvet_students');
    }
}

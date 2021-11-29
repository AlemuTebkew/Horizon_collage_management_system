<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDegreeStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('degree_students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('sex');
            $table->date('dob');
            $table->string('phone_no');
            $table->string('marital_status');
            $table->string('emergency_contact_name');
            $table->string('emergency_contact_relationShip');
            $table->string('emergency_contact_phone_no');
            $table->string('EGSSE_result');
            $table->string('EHEEE_result');
            //foreign keys from address table
            // $table->string('place_of_birth_address');
            // $table->string('current_residential_address');
            // $table->string('emergency_contact_address');
            $table->foreignId('birth_address_id')->constrained('addresses','id')->cascadeOnDelete()->cascadeOnUpdate();//place_of_birth_address
            $table->foreignId('residential_address_id')->constrained('addresses','id')->cascadeOnDelete()->cascadeOnUpdate();//current_residential_address
            $table->foreignId('contact_address_id')->constrained('addresses','id')->cascadeOnDelete()->cascadeOnUpdate();//emergency_contact_address

            $table->foreignId('program_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('degree_department_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            //
            $table->string('financialSource');
            $table->string('current_semester_no');
            $table->string('current_year_no');
            $table->string('batch');
            $table->string('employment_profile');
            $table->boolean('isGraduated');
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
        Schema::dropIfExists('degree_students');
    }
}

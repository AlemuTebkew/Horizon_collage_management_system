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
            $table->string('student_id',50)->unique()->nullable();
            $table->string('first_name',20);
            $table->string('middle_name',20);
            $table->string('last_name',20);
            $table->string('sex',10);
            $table->date('dob');
            $table->string('phone_no',20)->nullable();
            $table->string('residence_tel',20)->nullable();
            $table->string('residence_office_tel',20)->nullable();
            $table->string('maritial_status',20)->nullable();
            $table->string('contact_full_name',30)->nullable();
            $table->string('contact_relationship',30)->nullable();
            $table->string('contact_tel',20)->nullable();
            $table->string('contact_phone_no',20)->nullable();
            $table->string('contact_office_tel',20)->nullable();
            $table->double('EGSSE_result')->nullable();
            $table->double('EHEEE_result')->nullable();

            $table->foreignId('birth_address_id')->constrained('addresses','id')->cascadeOnDelete()->cascadeOnUpdate();//place_of_birth_address
            $table->foreignId('residential_address_id')->constrained('addresses','id')->cascadeOnDelete()->cascadeOnUpdate();//current_residential_address
            $table->foreignId('emergency_address_id')->constrained('addresses','id')->cascadeOnDelete()->cascadeOnUpdate();//emergency_contact_address

            $table->foreignId('degree_department_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            //
            $table->string('financial_source',50)->nullable();
            $table->integer('current_semester_no');
            $table->integer('current_year_no');
            $table->string('batch');
            $table->boolean('is_graduated')->default(0);
            $table->boolean('fully_scholarship')->default(0);
            $table->date('graduated_date')->nullable();
            $table->softDeletes();
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

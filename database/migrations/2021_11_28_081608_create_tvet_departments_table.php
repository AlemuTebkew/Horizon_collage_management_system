<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTvetDepartmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tvet_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name')->unique();

            $table->string('sector')->nullable();
            //Any additional column modifiers must be called before the constrained method:
            $table->foreignId('department_head_id')->nullable()
                   ->constrained('employees','id')->nullOnDelete();

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
        Schema::dropIfExists('tvet_departments');
    }
}

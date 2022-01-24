<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentLevelModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_level_module', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tvet_student_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->double('from_20')->default(0);
            $table->double('from_30')->default(0);
            $table->double('from_50')->default(0);
            $table->double('total_mark')->default(0.0);
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
        Schema::dropIfExists('student_level_module');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemestersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_close')->default(0);
            $table->boolean('is_current')->default(1);

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
        Schema::dropIfExists('semesters');
    }
}

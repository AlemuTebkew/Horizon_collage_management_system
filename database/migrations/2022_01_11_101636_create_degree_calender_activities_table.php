<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDegreeCalenderActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('degree_calender_activities', function (Blueprint $table) {
            $table->id();
            $table->string('activity')->nullable();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('date')->nullable();
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
        Schema::dropIfExists('calender_activities');
    }
}

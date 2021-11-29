<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTvetSectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tvet_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tvet_department_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('level_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('tvet_sections');
    }
}

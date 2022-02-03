<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDynamicSystemSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dynamic_system_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('degree_teacher_result_entry_time')->default(0);
            $table->boolean('tvet_teacher_result_entry_time')->default(0);
            $table->boolean('degree_registrar_result_entry_time')->default(0);
            $table->boolean('tvet_registrar_result_entry_time')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dynamic_system_settings');
    }
}

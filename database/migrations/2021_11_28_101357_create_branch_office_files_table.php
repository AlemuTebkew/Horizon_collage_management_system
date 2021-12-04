<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchOfficeFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_office_file', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_office_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('file_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('description');

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
        Schema::dropIfExists('branch_office_files');
    }
}

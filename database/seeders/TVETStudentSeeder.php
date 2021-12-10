<?php

namespace Database\Seeders;

use App\Models\TvetStudent;
use Illuminate\Database\Seeder;

class TvetStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TvetStudent::factory()->count(50)->create();

    }
}

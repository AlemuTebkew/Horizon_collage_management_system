<?php

namespace Database\Seeders;

use App\Models\DegreeStudent;
use Illuminate\Database\Seeder;

class DegreeStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DegreeStudent::factory()->count(50)->create();

    }
}

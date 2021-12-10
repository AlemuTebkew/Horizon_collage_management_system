<?php

namespace Database\Factories;
use App\Models\Address;
use App\Models\Program;
use App\Models\TvetDepartment;

use Illuminate\Database\Eloquent\Factories\Factory;

class TvetStudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name'=>$this->faker->firstName,
            'last_name'=>$this->faker->lastName,
            'phone_no'=>$this->faker->phoneNumber,
            'sex'=>$this->faker->randomElement(['Male','Female']),
            'dob'=>$this->faker->date,
            'martial_status'=>$this->faker->randomElement(['single','maried']),
            'emergency_contact_name'=>$this->faker->firstName,
            'emergency_contact_relationship'=>$this->faker->word,
            'emergency_contact_phone_no'=>$this->faker->phoneNumber,
            'EGSSE_result'=>$this->faker->randomDigit(2,4),
            'EHEEE_result'=>$this->faker->numberBetween(300,700),
            'program_id'=>Program::inRandomOrder()->first()->id,
            'tvet_department_id'=>TvetDepartment::inRandomOrder()->first()->id,
            'financial_source'=>$this->faker->sentence(10,true),
            'no_of_level'=>$this->faker->numberBetween(1,5),
            'current_level_no'=>1,
            'batch'=> $this->faker->year ,
            'fully_scolarship'=>$this->faker->randomElement(1,2),
            'birth_address_id'=>Address::inRandomOrder()->first()->id,
            'residential_address_id'=>Address::inRandomOrder()->first()->id,
            'emergency_address_id'=>Address::inRandomOrder()->first()->id,

        ];
    }
}

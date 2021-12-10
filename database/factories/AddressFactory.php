<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [

            'country'=>$this->faker->country,
            'region'=>$this->faker->city(),
            'zone'=>$this->faker->city,
            'subcity'=>$this->faker->country,
            'kebele'=>$this->faker->city(),
            'house_no'=>$this->faker->randomDigitNotNull(),
            'town'=>$this->faker->city,
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MonthFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'first_name'=>'Tewachew',
            'last_name'=>'Addis',
            'phone_no'=>'0912123234',
            'sex'=>'Male',
            'role'=>'admin',
            'email'=>'tewachew@gmail.com',
        ];
    }
}

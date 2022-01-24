<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserLoginFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [

            'password'=> Hash::make('Addis1234'),
            'user_type'=>'employee',
            'user_name'=>'tewachew@gmail.com',
        ];
    }
}

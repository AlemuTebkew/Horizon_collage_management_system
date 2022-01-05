<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'=>$this->faker->title,
            'description'=>$this->faker->sentence(100),
            'event_start_time'=>$this->faker->time,
            'event_end_time'=>$this->faker->time,
            'posted_date'=>$this->faker->date,

        ];
    }
}

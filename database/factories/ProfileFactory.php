<?php

namespace Database\Factories;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'user_id'=>User::factory(),
        'bio'=>fake()->sentence(10),
        'phone'=>fake()->unique()->phoneNumber(),
        'avatar'=>null,
        'gender'=>fake()->randomElement(['Male','Female','Other']),
        'dob'=>fake()->date(),
        'country'=>fake()->country(),
        'city'=>fake()->city(),
        ];
    }
}

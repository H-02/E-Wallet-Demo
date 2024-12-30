<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'mobile_number' => $this->faker->unique()->phoneNumber,
            'password' => bcrypt('password'),
            'wallet_balance' => $this->faker->randomFloat(2, 0, 1000),
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'role' => 'USER'
        ];
    }
}

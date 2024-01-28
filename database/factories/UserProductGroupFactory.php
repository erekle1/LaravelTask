<?php

namespace Database\Factories;

use App\Models\UserProductGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class UserProductGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = UserProductGroup::class;

    public function definition(): array
    {
        return [
            'user_id' =>User::factory(),
            'discount' => $this->faker->randomFloat(2, 0, 100) // discount percentage
        ];
    }
}

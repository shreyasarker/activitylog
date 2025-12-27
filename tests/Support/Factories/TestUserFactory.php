<?php

namespace ShreyaSarker\Activitylog\Tests\Support\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use ShreyaSarker\Activitylog\Tests\Support\TestUser;

class TestUserFactory extends Factory
{
    protected $model = TestUser::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
        ];
    }
}

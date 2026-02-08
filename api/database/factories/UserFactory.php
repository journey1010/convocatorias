<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Modules\User\Models\User;
use Modules\User\Enums\StatusUser;
use Modules\User\Enums\TypeUser;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'dni' => (string) fake()->unique()->numberBetween(10000000, 99999999),
            'nickname' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->phoneNumber(),
            'password' => static::$password ??= Hash::make('password'),
            'status' => StatusUser::ACTIVE->value,
            'type_user' => TypeUser::citizen->value,
            'level' => 1,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function citizen(): static
    {
        return $this->state(fn(array $attributes) => [
            'type_user' => TypeUser::citizen->value
        ]);
    }

    public function employee(): static 
    {
        return $this->state(fn(array $attributes) => [
            'type_user' => TypeUser::employee->value
        ])->afterCreating(function (User $user) {
            \Modules\User\Models\OfficeUser::factory()->create([
                'user_id' => $user->id
            ]);
        });
    }
}
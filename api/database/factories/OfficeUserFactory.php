<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\User\Models\OfficeUser;
use Modules\User\Models\User;
use Modules\Office\Models\Office;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<OfficeUser>
 */
class OfficeUserFactory extends Factory
{
    protected $model = OfficeUser::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'office_id' => Office::factory(),
        ];
    }
}

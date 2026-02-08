<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Office\Models\Office;
use Modules\Office\Models\Locale;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Office>
 */
class OfficeFactory extends Factory
{
    protected $model = Office::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'locale_id' => Locale::factory(),
            'status' => 1,
            'level' => 1,
        ];
    }
}

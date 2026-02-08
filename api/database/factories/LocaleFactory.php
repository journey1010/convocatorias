<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Office\Models\Locale;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Locale>
 */
class LocaleFactory extends Factory
{
    protected $model = Locale::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
        ];
    }
}

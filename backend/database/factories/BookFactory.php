<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Number;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'=>fake()->word(),
            'page_count'=>fake()->numberBetween(50,500),
            'publishing_year'=>(int)fake()->year(2025),
            'author'=>fake()->firstName().' '.fake()->lastName(),
            'edition'=>Number::ordinal(fake()->numberBetween(1,20)),
            'number_of_copies'=>fake()->numberBetween(1,100),
            'image'=>fake()->url(),
            'notes'=>fake()->optional(0.3)->text(150),
        ];
    }
}

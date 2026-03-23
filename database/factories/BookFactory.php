<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Author;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
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
            'title' => $this->faker->sentence(3),
            'isbn' => $this->faker->unique()->isbn13(),
            'description' => $this->faker->paragraph(),
            'published_date' => $this->faker->date(),
            'author_id' => Author::inRandomOrder()->first()?->id ?? Author::factory(),
            'genre' => $this->faker->randomElement(['Fiction', 'Non-Fiction', 'Science Fiction', 'Mystery', 'Biography']),
            'total_copies' => $total = $this->faker->numberBetween(1, 100),
            'available_copies' => $this->faker->numberBetween(0, $total),
            'price' => $this->faker->randomFloat(2, 5, 100),
            'cover_image' => $this->faker->imageUrl(200, 300, 'books', true),
            'status' => $this->faker->randomElement(['active', 'inactive']),
        ];
    }
}

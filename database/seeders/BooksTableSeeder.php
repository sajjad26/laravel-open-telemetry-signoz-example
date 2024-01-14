<?php

namespace Database\Seeders;

use App\Models\Author;
use Faker\Factory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();
        $authors = Author::all();

        foreach (range(1, 50) as $index) {
            $book = Book::create([
                'name' => $faker->sentence(),
                'summary' => $faker->sentence(100),
            ]);

            // Attach random authors to the book
            if ($authors->count() > 0) {
                $randomAuthors = $authors->random(rand(1, 3)); // Choose 1-3 random authors
                $book->authors()->attach($randomAuthors);
            }
        }
    }
}

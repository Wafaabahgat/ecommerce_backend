<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Admin\Category;
use App\Models\Admin\Product;
use App\Models\Admin\Store;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(UserSeeder::class);

        Category::create([
            'name' => 'Laptop',
            'slug' => 'laptop',
        ]);

        Store::create([
            'name' => 'HP',
            'slug' => 'hp',
        ]);

        Product::create([
            "store_id" => 1,
            "category_id" => 1,
            "name" => 'hp Laptop',
            "slug" => 'hp-laptop',
            "disc" => 'this is a long text about labtob',
            "image" => fake()->imageUrl(600, 600, 'laptop'),
            "price" => 1000,
        ]);
    }
}

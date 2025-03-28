<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Str;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $categories = ['Tech', 'Health', 'Education', 'Business', 'Entertainment'];

        foreach ($categories as $category) {
            Category::create([
                'title' => $category,
                'description' => 'Description for ' . $category,
                'slug' => Str::slug($category),
                'user_id' => $admin->id
            ]);
        }
    }
}

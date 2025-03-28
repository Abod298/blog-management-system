<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Str;

class PostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $users = User::all();

        for ($i = 1; $i <= 20; $i++) {
            $post = Post::create([
                'title' => "Post Title $i",
                'body' => "Body content for post $i.",
                'slug' => Str::slug("Post Title $i"),
                'user_id' => $users->random()->id,
                'published_at' => Carbon::now()->subDays(rand(1, 30))
            ]);

            $post->categories()->attach($categories->random(rand(1, 3))->pluck('id'));
             $post->addMedia(public_path('/images/laravel.jpg'))->preservingOriginal()->toMediaCollection('images');
        }
    }
}

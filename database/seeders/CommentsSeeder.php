<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posts = Post::all();
        $admin = User::where('email', 'admin@test.com')->first();
        $users = User::all();

        for ($i = 1; $i <= 60; $i++) {
            $comment = Comment::create([
                'body' => "This is comment number $i.",
                'post_id' => $posts->random()->id,
                'user_id' => $users->random()->id,
                'confirmed_by' => $admin->id,
                'confirmed_at' => Carbon::now()->subDays(rand(1, 30))
            ]);
        }
    }
}

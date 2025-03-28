<?php

namespace App\Repositories;

use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

class PostRepository implements PostRepositoryInterface
{
    public function getLatestPosts(): Collection
    {
        return Post::with([
                'categories',
                'comments.user.media',
                'author.media',
                'media'
            ])
            ->whereNotNull('published_at')
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getRelatedPosts(int $limit = 10): Collection
    {
        return Post::with([
                'categories',
                'comments.user.media',
                'author.media',
                'media'
            ])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPostBySlug(string $slug): ?Post
    {
        return Post::with([
                'categories',
                'comments.user.media',
                'author.media',
                'media'
            ])
            ->where('slug', $slug)
            ->first();
    }
}

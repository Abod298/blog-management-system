<?php

namespace App\Interfaces;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface
{
    public function getLatestPosts(): Collection;
    public function getRelatedPosts(): Collection;
    public function getPostBySlug(string $slug): ?Post;
}

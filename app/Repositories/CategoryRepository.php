<?php

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAllCategories(): Collection
    {
        return Category::with([
            'posts.media',
            'user.media',
            'media',
            'posts' => fn($query) => $query->published(), 
        ])->get();
    }

    public function getCategory(string $slug): ?Category
    {
        return Category::where('slug', $slug)
            ->with([
                'posts.media',
                'user.media',
                'media',
                'posts' => fn($query) => $query->published(),
            ])
            ->first();
    }
}

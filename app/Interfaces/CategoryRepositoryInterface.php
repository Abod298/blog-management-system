<?php

namespace App\Interfaces;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function getAllCategories(): Collection;
    public function getCategory(string $slug): ? Category;
}

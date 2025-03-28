<?php
namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository implements UserRepositoryInterface
{
    public function getLatestPosts(int $limit = 10): Collection
    {
        return User::with(['categories', 'comments', 'user'])
            ->latest()
            ->take($limit)
            ->get();
    }
}

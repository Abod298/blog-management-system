<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; // All users can view posts
    }

    public function view(User $user, Post $post): bool
    {
        return true; // All users can view individual posts
    }

    public function create(User $user): bool
    {
        return true; // All authenticated users can create posts
    }

    public function update(User $user, Post $post): bool
    {
        return $user->getIsAdminAttribute() || $user->id === $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->getIsAdminAttribute() || $user->id === $post->user_id;
    }

}

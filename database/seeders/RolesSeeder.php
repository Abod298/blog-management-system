<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::firstOrCreate(['title' => 'Admin']);
        $author = Role::firstOrCreate(['title' => 'Author']);
        $user = Role::firstOrCreate(['title' => 'User']);

        $admin->permissions()->sync(Permission::pluck('id')->toArray());

        $author->permissions()->sync(Permission::whereIn('title', [
            'create-posts', 'edit-posts', 'delete-posts', 'access-posts', 'access-categories',
            'create-comments', 'edit-comments', 'delete-comments', 'access-comments'
        ])->pluck('id')->toArray());

        $user->permissions()->sync(Permission::whereIn('title', [
            'create-comments', 'access-comments',  'delete-comments', 'access-posts', 'access-categories',
        ])->pluck('id')->toArray());
    }
}

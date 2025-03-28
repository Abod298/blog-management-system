<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'create-categories', 'edit-categories', 'delete-categories', 'access-categories',
            'create-posts', 'edit-posts', 'delete-posts', 'access-posts',
            'create-comments', 'edit-comments', 'delete-comments','confirm-comments', 'access-comments',
            'create-users', 'edit-users', 'delete-users', 'access-users'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['title' => $permission]);
        }
    }
}

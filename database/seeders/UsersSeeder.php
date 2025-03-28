<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['Admin', 'Author', 'User'];
        $phoneIndex = 1;

        foreach ($roles as $role) {
            $user = User::create([
                'name' => $role,
                'last_name' => 'Test',
                'email' => strtolower($role) . '@test.com',
                'phone' => '12345678' . str_repeat($phoneIndex, $phoneIndex),
                'password' => Hash::make('password'),
            ]);

            $user->roles()->attach(Role::where('title' , $role)->first()->id);
            $phoneIndex++;
        }
    }
}

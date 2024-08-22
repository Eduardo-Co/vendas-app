<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->count(30)
            ->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'profile' => 'administrator',
            'status' => 'actived',
            'gender' => 'male', 
        ]);

        User::factory()->create([
            'name' => 'Normal User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'), 
            'profile' => 'user',
            'status' => 'actived',
            'gender' => 'female', 
        ]);
    }
}

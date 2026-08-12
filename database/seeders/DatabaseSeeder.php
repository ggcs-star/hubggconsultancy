<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@presalesschool.test'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'profile_completed' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'user@presalesschool.test'],
            [
                'name' => 'Demo Salesperson',
                'password' => Hash::make('password'),
                'role' => 'user',
            ]
        );

        $this->call(SaasProductSeeder::class);
    }
}

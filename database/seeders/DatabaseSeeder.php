<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        $admin = \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@smarttravel.co.tz',
            'phone' => '0623344513',
            'password' => Hash::make('superadmin@smarttravel2023'),
            'registration_verification' => 1
        ]);

        $admin->assignRole('dashboard-user');
    }
}
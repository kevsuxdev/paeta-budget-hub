<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $users = [
            [
                'full_name' => 'Administrator',
                'email' => 'admin@paete.gov.ph',
                'role' => 'admin',
            ],
            [
                'full_name' => 'Staff',
                'email' => 'staff@paete.gov.ph',
                'role' => 'staff',
            ],
            [
                'full_name' => 'Finance',
                'email' => 'finance@paete.gov.ph',
                'role' => 'finance',
            ],
            [
                'full_name' => 'Department Head kevs',
                'email' => 'head@paete.gov.ph',
            ]
        ];

        User::factory()->createMany($users);
    }
}

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
                'email' => 'admin@paeta.gov.ph',
                'role' => 'admin',
            ],
            [
                'full_name' => 'Staff',
                'email' => 'staff@paeta.gov.ph',
                'role' => 'staff',
            ],
            [
                'full_name' => 'Finance',
                'email' => 'finance@paeta.gov.ph',
                'role' => 'finance',
            ],
            [
                'full_name' => 'Department Head kevs',
                'email' => 'head@paeta.gov.ph',
            ]
        ];

        User::factory()->createMany($users);
    }
}

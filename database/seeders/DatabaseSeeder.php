<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {

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
                'full_name' => 'Department Head',
                'email' => 'head@paete.gov.ph',
                'role' => 'dept_head',
            ]
        ];

        User::factory()->createMany($users);
    }
}

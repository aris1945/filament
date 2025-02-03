<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nik' => '18990339', // NIK contoh
            'name' => 'Aris',
            'email' => 'aris@example.com',
            'password' => bcrypt('18990339'), // Hashing password agar aman
        ]);
    }
}

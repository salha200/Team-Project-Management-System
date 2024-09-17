<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\DeveloperSeeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DeveloperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'developer',
            'email' => 'developer@email.com',
            'password' =>Hash::make('12345678'),
            'role' => 'developer',
        ]);
    }
}

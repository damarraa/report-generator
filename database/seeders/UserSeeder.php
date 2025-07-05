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
        $user1 = User::create([
            'name' => 'Saipul',
            'email' => 'saiful@prisan.co.id',
            'password' => Hash::make('jointer01'),
            'is_active' => true,
        ]);

        $user1->assignRole('Petugas');

        $user2 = User::create([
            'name' => 'Syahruddin',
            'email' => 'saruddin1908@gmail.com',
            'password' => Hash::make('jointer02'),
            'is_active' => true,
        ]);

        $user2->assignRole('Petugas');

        $user3 = User::create([
            'name' => 'Badrul',
            'email' => 'badrul290694@gmail.com',
            'password' => Hash::make('jointer03'),
            'is_active' => true,
        ]);

        $user3->assignRole('Petugas');
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Administradores
        User::create([
            'name' => 'Martha Landa',
            'username' => 'martha',
            'password' => Hash::make('020495'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Orlando Torres',
            'username' => 'orlando',
            'password' => Hash::make('930225'),
            'role' => 'admin',
        ]);
        User::create([
            'name' => 'Yanet Landa',
            'username' => 'yanet',
            'password' => Hash::make('170589'),
            'role' => 'admin',
        ]);

        // Usuario estándar
        User::create([
            'name' => 'Emily Reyes',
            'username' => 'emily',
            'password' => Hash::make('050807'),
            'role' => 'user',
        ]);
    }
}
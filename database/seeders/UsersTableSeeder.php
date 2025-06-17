<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 12,
                'name' => 'Afiq Fitri',
                'email' => 'afiqzuliey@gmail.com',
                'password' => Hash::make('afiq1234'),
                'role' => 'PublicUser',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 13,
                'name' => 'MCMC Admin',
                'email' => 'afiqf330@gmail.com',
                'password' => Hash::make('afiq1234'),
                'role' => 'MCMC',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 14,
                'name' => 'JPJ Agency',
                'email' => 'jpj@example.com',
                'password' => Hash::make('password'),
                'role' => 'Agency',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 15,
                'name' => 'Khusnul',
                'email' => 'khusnul.sudjiamin@gmail.com',
                'password' => Hash::make('khusnul1234'),
                'role' => 'PublicUser',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}

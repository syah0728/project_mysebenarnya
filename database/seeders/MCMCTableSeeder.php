<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MCMCTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mcmc')->insert([
            'id' => 3,
            'user_id' => 13,
            'username' => 'mcmcadmin',
            'phone' => '01130484877',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            PublicUserTableSeeder::class,
            MCMCTableSeeder::class,
            AgencyTableSeeder::class,
            InquiryTableSeeder::class,
            AssignmentTableSeeder::class,
            ProgressTableSeeder::class,
        ]);
    }

}

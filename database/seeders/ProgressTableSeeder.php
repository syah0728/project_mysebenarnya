<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgressTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('progress')->insert([
            'id' => 16,
            'Inquiry_id' => 12,
            'UpdateDate' => now()->toDateString(),
            'ProgressStatus' => 'Under Investigation',
            'ProgressDescription' => 'JPJ is reviewing the claim internally.',
            'ReviewingOfficer' => 'En. Hamzah',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

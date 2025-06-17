<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignmentTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('assignment')->insert([
            'id' => 11,
            'Inquiry_id' => 12,
            'Agency_id' => 5,
            'PublicUser_id' => 6,
            'AssignmentDate' => now()->toDateString(),
            'due_date' => now()->addDays(5)->toDateString(),
            'comments' => 'Please investigate the viral claim.',
            'AssignmentStatus' => 'Assigned',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}

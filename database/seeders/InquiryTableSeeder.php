<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InquiryTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('inquiry')->insert([
            [
                'id' => 12,
                'PublicUser_id' => 6,
                'NewsTitle' => 'Social Media Post About Traffic Fine Discount',
                'NewsContent' => 'A viral post claims JPJ is giving a 50% discount on traffic fines for a week.',
                'NewsSource' => 'https://twitter.com/fakeclaim',
                'InquiryDate' => now()->format('Y-m-d H:i:s'),
                'InquiryStatus' => 'Pending',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 13,
                'PublicUser_id' => 7,
                'NewsTitle' => 'Fake COVID-19 Vaccine Certificate Circulating',
                'NewsContent' => 'Screenshots showing a "free vaccine cert" site went viral. Is it legit?',
                'NewsSource' => 'https://telegram.link/fakevax',
                'InquiryDate' => now()->subDays(1)->format('Y-m-d H:i:s'),
                'InquiryStatus' => 'Assigned',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 14,
                'PublicUser_id' => 7,
                'NewsTitle' => 'Government Will Give Free Laptops to All Students',
                'NewsContent' => 'A Facebook page claims all students will receive free laptops next month.',
                'NewsSource' => 'https://facebook.com/laptop-gov-claim',
                'InquiryDate' => now()->subDays(2)->format('Y-m-d H:i:s'),
                'InquiryStatus' => 'Under Investigation',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 15,
                'PublicUser_id' => 6,
                'NewsTitle' => 'Petrol Price Drop to RM1.50 Next Week',
                'NewsContent' => 'Post on WhatsApp chain message claims fuel prices will drop significantly.',
                'NewsSource' => 'WhatsApp Message',
                'InquiryDate' => now()->subDays(3)->format('Y-m-d H:i:s'),
                'InquiryStatus' => 'Resolved',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 16,
                'PublicUser_id' => 6,
                'NewsTitle' => 'MCMC Shutting Down TikTok in Malaysia',
                'NewsContent' => 'An IG post claims that MCMC will ban TikTok by next month.',
                'NewsSource' => 'https://instagram.com/fakepost',
                'InquiryDate' => now()->subDays(4)->format('Y-m-d H:i:s'),
                'InquiryStatus' => 'Verified',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}

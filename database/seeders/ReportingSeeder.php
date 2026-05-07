<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    \App\Models\Reporting::create([
        'user_id' => 1, // Assure-toi que l'ID 1 existe dans ta table users
        'campaign_id' => 1, // Assure-toi que l'ID 1 existe dans ta table campaigns
        'report_date' => now(),
        'calls_count' => 50,
        'success_count' => 12,
        'dmc' => 4.5,
        'comment' => 'Bonne session de test'
    ]);
    }
}

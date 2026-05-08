<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Campaign;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $campaigns = [
            [
                'name' => 'Refonte Site E-commerce',
                'description' => 'Refonte complète du site e-commerce avec nouvelles fonctionnalités et design moderne',
                'start_date' => '2024-01-15',
                'end_date' => '2024-06-30',
                'status' => 'active',
            ],
            [
                'name' => 'Application Mobile Banking',
                'description' => 'Développement d\'une application mobile pour la banque en ligne',
                'start_date' => '2024-02-01',
                'end_date' => '2024-12-31',
                'status' => 'active',
            ],
            [
                'name' => 'CRM Integration',
                'description' => 'Intégration d\'un nouveau système CRM avec l\'infrastructure existante',
                'start_date' => '2024-03-15',
                'end_date' => '2024-08-15',
                'status' => 'active',
            ],
            [
                'name' => 'Migration Cloud Infrastructure',
                'description' => 'Migration des serveurs locaux vers une infrastructure cloud sécurisée',
                'start_date' => '2023-09-01',
                'end_date' => '2024-03-31',
                'status' => 'completed',
            ],
            [
                'name' => 'Data Analytics Platform',
                'description' => 'Plateforme d\'analyse de données pour les décisions business',
                'start_date' => '2024-05-01',
                'end_date' => '2024-11-30',
                'status' => 'active',
            ],
            [
                'name' => 'Security Audit',
                'description' => 'Audit de sécurité complet de l\'infrastructure IT',
                'start_date' => '2024-04-01',
                'end_date' => '2024-07-31',
                'status' => 'inactive',
            ],
        ];

        foreach ($campaigns as $campaign) {
            Campaign::create($campaign);
        }
    }
}

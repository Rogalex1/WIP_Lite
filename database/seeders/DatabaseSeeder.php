<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            PositionSeeder::class,
            // UserSeeder::class,
            EmployeeSeeder::class,
            CampaignSeeder::class,
            AssignmentSeeder::class,
            PlanningModelSeeder::class,
            PlanningAssignmentSeeder::class,
            TimesheetSeeder::class,
            TimesheetEntrySeeder::class,
            ActivityLogSeeder::class,
            EmployeeHistorySeeder::class,
            AssignmentHistorySeeder::class,
            PlanningHistorySeeder::class,
            TimesheetHistorySeeder::class,
        ]);
    }
}

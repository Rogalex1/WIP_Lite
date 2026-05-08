<?php

namespace App\Http\Controllers\Tc;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Campaign;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Notification;

class TcController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $assignment = Assignment::where('employee_id', $employee->id)
            ->where('status', 'active')
            ->with(['campaign', 'manager'])
            ->first();

        $todaySchedule = [
            'morning_start' => '08:00',
            'morning_end' => '12:00',
            'lunch_break' => '12:00 - 13:00',
            'afternoon_start' => '13:00',
            'afternoon_end' => '17:00',
        ];

        return Inertia::render('Dashboard/TCDashboard', [
            'my_stats' => [
                'hours_done' => 142,
                'quality_score' => 95,
                'off_days' => 12,
            ],
            'today_schedule' => $todaySchedule,
            'current_campaign' => $assignment ? $assignment->campaign : null,
        ]);
    }
}

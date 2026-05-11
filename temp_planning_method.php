    public function planning()
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        // Récupérer les plannings du TC et de son SUP
        $planningAssignments = PlanningAssignment::where(function ($query) use ($employee) {
            $query->where('employee_id', $employee->id)
                  ->orWhereHas('employee.assignments', function ($q) use ($employee) {
                      $q->where('employee_id', $employee->id)
                        ->where('status', 'active');
                  });
        })
        ->with(['planningModel', 'validator', 'employee'])
        ->orderByDesc('start_date')
        ->get();

        return Inertia::render('Tc/Planning', [
            'current_campaign' => $employee->assignments()->where('status', 'active')->with('campaign')->first()?->campaign,
            'planning_assignments' => $planningAssignments,
            'today_schedule' => [
                'morning_start' => '08:00',
                'morning_end' => '12:00',
                'lunch_break' => '12:00 - 13:00',
                'afternoon_start' => '13:00',
                'afternoon_end' => '17:00',
            ],
        ]);
    }

<?php

namespace App\Services;

use App\Models\EmployeeActivity;
use Illuminate\Support\Facades\Auth;

class EmployeeActivityLogger
{
    /**
     * Log an activity for the current logged in employee.
     */
    public static function log(string $type, string $description, array $metadata = null)
    {
        $employee = Auth::guard('web')->user();
        
        if (!$employee) {
            return;
        }

        EmployeeActivity::create([
            'employee_id' => $employee->id,
            'activity_type' => $type,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Log an activity for a specific employee.
     */
    public static function logFor(int $employeeId, string $type, string $description, array $metadata = null)
    {
        EmployeeActivity::create([
            'employee_id' => $employeeId,
            'activity_type' => $type,
            'description' => $description,
            'metadata' => $metadata,
        ]);
    }
}

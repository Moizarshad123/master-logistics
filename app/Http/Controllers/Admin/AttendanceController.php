<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        $drivers = Driver::where('status', 'active')->orderBy('name')->get();

        // Current month default
        $selectedMonth = request('month', now()->format('Y-m'));

        // Existing attendance for selected month (keyed by driver_id)
        $attendances = Attendance::where('month', $selectedMonth)
            ->get()
            ->keyBy('driver_id');

        return view('admin.attendance.index', compact('drivers', 'selectedMonth', 'attendances'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'month'      => 'required|date_format:Y-m',
                'attendance' => 'required|array',
                'attendance.*.present_days' => 'required|numeric|min:0',
                'attendance.*.absent_days'  => 'required|numeric|min:0',
                'attendance.*.leave_days'   => 'required|numeric|min:0',
            ], [
                'month.required'      => 'Month field is required.',
                'month.date_format'   => 'Month format must be YYYY-MM.',

                'attendance.required' => 'Attendance data is required.',
                'attendance.array'    => 'Attendance must be an array.',

                'attendance.*.present_days.required' => 'Present days are required.',
                'attendance.*.present_days.numeric'  => 'Present days must be a number.',
                'attendance.*.present_days.min'      => 'Present days cannot be negative.',

                'attendance.*.absent_days.required' => 'Absent days are required.',
                'attendance.*.absent_days.numeric'  => 'Absent days must be a number.',
                'attendance.*.absent_days.min'      => 'Absent days cannot be negative.',

                'attendance.*.leave_days.required' => 'Leave days are required.',
                'attendance.*.leave_days.numeric'  => 'Leave days must be a number.',
                'attendance.*.leave_days.min'      => 'Leave days cannot be negative.',
            ]);
            foreach ($request->attendance as $driverId => $data) {
                Attendance::updateOrCreate(
                    [
                        'driver_id' => $driverId,
                        'month'     => $request->month,
                    ],
                    [
                        'present_days' => $data['present_days'] ?? 0,
                        'absent_days'  => $data['absent_days']  ?? 0,
                        'leave_days'   => $data['leave_days']   ?? 0,
                    ]
                );
            }
    
            return back()->with('success', 'Attendance saved successfully for ' . $request->month);
        } catch (\Exception $ex) {
            return redirect('admin/attendance')->with('error', $ex->getMessage());
        }
    }
}
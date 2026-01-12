<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use Carbon\Carbon;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index()
    {
        $drivers = Driver::orderBy('name')->get();
        return view('admin.attendance.index', compact('drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date'       => 'required|date',
            'attendance' => 'required|array'
        ]);

        // Off day check (Sunday)
        if (Carbon::parse($request->date)->isSunday()) {
            return back()->with('error', 'Sunday is off day');
        }

        $alreadyMarked = Attendance::whereDate('date', $request->date)->exists();

        if ($alreadyMarked) {
            return back()->with('error', 'Attendance already marked for this date');
        }

        foreach ($request->attendance as $driverId => $status) {
            Attendance::updateOrCreate(
                [
                    'driver_id' => $driverId,
                    'date'      => $request->date
                ],
                [
                    'status' => $status
                ]
            );
        }

        return back()->with('success', 'Attendance saved successfully');
    }
}

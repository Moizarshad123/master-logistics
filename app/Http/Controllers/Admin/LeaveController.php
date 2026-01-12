<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
        ]);

        Leave::create($request->all());

        return response()->json(['message' => 'Leave request submitted']);
    }

    public function approve($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->update(['status' => 'approved']);

        // Auto mark attendance as leave
        for ($date = Carbon::parse($leave->from_date);
             $date->lte($leave->to_date);
             $date->addDay()) {

            if ($date->isSunday()) continue;

            Attendance::updateOrCreate([
                'driver_id' => $leave->driver_id,
                'date' => $date->format('Y-m-d')
            ], [
                'status' => 'leave'
            ]);
        }

        return response()->json(['message' => 'Leave approved']);
    }

    public function reject($id)
    {
        Leave::findOrFail($id)->update(['status' => 'rejected']);
        return response()->json(['message' => 'Leave rejected']);
    }
}

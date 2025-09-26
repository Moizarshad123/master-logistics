<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\TripDetail;

class ReportController extends Controller
{
    public function tripVehicleReport() {
        $trips = Trip::with('tripDetails', 'vehicle', 'driver')->latest()->paginate(10);
        return view('admin.reports.trip_report', compact('trips'));
    }

    public function viewTripVehicleReport($tripId) {
        $trip = Trip::with(['vehicle', 'driver', 'tripDetails', 'tripPayments', 'tripExpenses', 'tripExpenses.expenseName'])->findOrFail($tripId);
        return view('admin.reports.view_trip_report', compact('trip'));
    }


    
}

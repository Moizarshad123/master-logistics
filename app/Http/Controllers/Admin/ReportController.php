<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\TripDetail;
use Carbon\Carbon;

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

    public function profit_and_loss(Request $request)
    {
        // Get selected date or default to today
        $date = $request->input('date', Carbon::today()->toDateString());

        // Get all trips with details & expenses
        $trips = Trip::with(['tripDetails', 'tripExpenses', 'vehicle'])
                        ->whereDate('trip_date', $date)
                        ->get();

        $totalIncome   = 0;
        $totalExpenses = 0;

        // Map trips with breakdowns
        $tripData = $trips->map(function ($trip) use (&$totalIncome, &$totalExpenses) {
            $income          = $trip->tripDetails->sum('rent');
            $weeklyLabour    = $trip->tripDetails->sum('weekly_labour');
            $balochLabour    = $trip->tripDetails->sum('baloch_labour');
            $vehicleExpenses = $trip->tripExpenses->sum('amount');

            $expenses = $weeklyLabour + $balochLabour + $vehicleExpenses;
            $profit   = $income - $expenses;

            // Add to totals
            $totalIncome   += $income;
            $totalExpenses += $expenses;

            return [
                'trip_no'           => $trip->trip_no,
                'vehicle'           => $trip->vehicle->vehicle_no ?? 'N/A',
                'income'           => $income,
                'weekly_labour'    => $weeklyLabour,
                'baloch_labour'    => $balochLabour,
                'vehicle_expenses' => $trip->tripExpenses,
                'total_expenses'   => $expenses,
                'profit'           => $profit,
            ];
        });

        $grandProfit = $totalIncome - $totalExpenses;

        return view('admin.reports.profit_loss', compact('tripData', 'date', 'totalIncome', 'totalExpenses', 'grandProfit'));
    }

    public function weekly_labour_report(Request $request) {

        // $date    = $request->input('date', Carbon::today()->toDateString());
        $reports = TripDetail::with('trip', "customer")
                            ->whereNotNull('weekly_labour')
                            ->where('weekly_labour', '!=', 0)
                            ->orderByDESC('id')
                            ->paginate(15);
        return view('admin.reports.weekly_labour', compact('reports'));
    }

    public function baloch_labour_report(Request $request) {

        // $date    = $request->input('date', Carbon::today()->toDateString());
        $reports = TripDetail::with('trip', "customer")
                            ->whereNotNull('baloch_labour')
                            ->where('baloch_labour', '!=', 0)
                            ->orderByDESC('id')
                            ->paginate(15);
        return view('admin.reports.baloch_labour', compact('reports'));
    }


    
}

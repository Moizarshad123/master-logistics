<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\TripDetail;
use Carbon\Carbon;
use DB;

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


        $query = TripDetail::with('trip.vehicle', 'customer')
                                ->whereNotNull('weekly_labour')
                                ->where('weekly_labour', '!=', 0)
                                ->where('weekly_labour', '!=', '');
                                if ($request->trip_no) {
                                    $query->where('trip_id', $request->trip_no);
                                }
                                // Filter: vehicle no
                                if ($request->vehicle_no) {
                                    $query->whereHas('trip.vehicle', function($q) use ($request) {
                                    $q->where('vehicle_no', 'LIKE', '%'.$request->vehicle_no.'%');
                                    });
                                }

                                // Filter: multi date range
                                if ($request->start_date) {
                                    $query->where('start_date', $request->start_date);
                                }
                                $reports = $query->orderByDesc('id')->paginate(15);

        return view('admin.reports.weekly_labour', compact('reports'));
    }

    // public function view_weekly_labour_report(Request $request) {

    //     $reports = TripDetail::with('trip.vehicle', 'customer')
    //                             ->whereNotNull('weekly_labour')
    //                             ->where('weekly_labour', '!=', 0)
    //                             ->where('weekly_labour', '!=', '')
    //                             ->orderByDesc('id')
    //                             ->paginate(15);
    //     return view('admin.reports.view_weekly_labour', compact('reports'));
    // }

    public function view_weekly_labour_report(Request $request)
    {
        $reports = TripDetail::with('trip.vehicle', 'customer')
            ->select(
                'trip_id',
                DB::raw('SUM(weekly_labour) as total_weekly_labour'),
                DB::raw('SUM(total_bags) as total_bags'),
                DB::raw('MIN(start_date) as start_date'),
                DB::raw('MAX(end_date) as end_date'),
                DB::raw('GROUP_CONCAT(material SEPARATOR ", ") as material_list'),
                DB::raw('GROUP_CONCAT(from_destination, " - ", to_destination SEPARATOR " , ") as destinations')
            )
            ->whereNotNull('weekly_labour')
            ->where('weekly_labour', '!=', 0)
            ->where('weekly_labour', '!=', '')
            ->groupBy('trip_id')
            ->orderByDesc('trip_id')
            ->paginate(15);

        return view('admin.reports.view_weekly_labour', compact('reports'));
    }


    public function baloch_labour_report(Request $request) {

        $query = TripDetail::with([
                                    'trip.vehicle',
                                    'customer'
                                ])
                            ->whereNotNull('baloch_labour')
                            ->where('baloch_labour', '!=', 0);
                            if ($request->trip_no) {
                                $query->where('trip_id', $request->trip_no);
                            }
                            // Filter: vehicle no
                            if ($request->vehicle_no) {
                                $query->whereHas('trip.vehicle', function($q) use ($request) {
                                $q->where('vehicle_no', 'LIKE', '%'.$request->vehicle_no.'%');
                                });
                            }
                            if ($request->start_date) {
                                $query->where('start_date', $request->start_date);
                            }
                            $reports = $query->orderByDesc('id')->paginate(15);
        return view('admin.reports.baloch_labour', compact('reports'));
    }

    public function view_baloch_labour_report(Request $request)
    {
        $reports = TripDetail::with('trip.vehicle', 'customer')
            ->select(
                'trip_id',
                DB::raw('SUM(baloch_labour) as total_baloch_labour'),
                DB::raw('SUM(total_bags) as total_bags'),
                DB::raw('MIN(start_date) as start_date'),
                DB::raw('MAX(end_date) as end_date'),
                DB::raw('GROUP_CONCAT(material SEPARATOR ", ") as material_list'),
                DB::raw('GROUP_CONCAT(from_destination, " - ", to_destination SEPARATOR " , ") as destinations')
            )
            ->whereNotNull('baloch_labour')
            ->where('baloch_labour', '!=', 0)
            ->where('baloch_labour', '!=', '')
            ->groupBy('trip_id')
            ->orderByDesc('trip_id')
            ->paginate(15);

        return view('admin.reports.view_baloch_labour', compact('reports'));
    }
}

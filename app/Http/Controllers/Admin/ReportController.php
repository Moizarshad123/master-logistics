<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Vehicle;


use App\Models\TripDetail;
use Carbon\Carbon;
use DB;

class ReportController extends Controller
{
    public function tripVehicleReport() {

        try {
            if (request()->ajax()) {
                $trips = Trip::with('tripDetails', 'vehicle', 'driver');
                return datatables()->eloquent($trips->orderByDesc('id'))

                    ->filterColumn('vehicle', function($query, $keyword) {
                        $query->whereHas('vehicle', function($q) use ($keyword) {
                            $q->where('vehicle_no', 'like', "%{$keyword}%");
                        });
                    })

                    ->filterColumn('driver', function($query, $keyword) {
                        $query->whereHas('driver', function($q) use ($keyword) {
                            $q->where('name', 'like', "%{$keyword}%");
                        });
                    })


                    ->addColumn('vehicle', function ($data) {
                        if($data->vehicle != null) {
                            return $data->vehicle->vehicle_no;
                        } else {
                            return "";
                        }
                    })
                    ->addColumn('driver', function ($data) {
                        return $data->driver->name ?? '-';
                    })
                     ->addColumn('total_journeys', function ($data) {
                        return $data->tripDetails->count();
                    })
                    ->editColumn('created_at', function ($data) {
                        return  date('d M Y', strtotime($data->created_at));
                    })   
                    ->addColumn('action', function ($data) {

                        $viewUrl = route('admin.viewTripVehicleReport', $data->id);
                        return '<a href="'.$viewUrl.'" class="btn btn-sm btn-info">View Report</a>';
                    })
                    ->rawColumns(['action', 'vehicle', 'driver', 'total_journeys'])->make(true);

            }
        } catch (\Exception $ex) {
            return redirect('admin/trip-vehicle-report')->with('error', $ex->getMessage());
        }

        return view('admin.reports.trip_report');
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

    public function vehicleSummaryReport(Request $request)
    {
        $query = Trip::with(['vehicle.new_wheeler', 'tripExpenses']);

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $query->whereBetween('trip_date', [
                Carbon::parse($request->from_date)->startOfDay(),
                Carbon::parse($request->to_date)->endOfDay(),
            ]);
        }

        $trips = $query->get();

        // Expense ID → DB Name mapping (DB ko touch nahi kar rahe)
        $expenseMap = [
            1 => 'Meal',
            2 => 'Fueling',
            3 => 'Service',
            4 => 'Route',
            5 => 'Toll Tax',
            6 => 'Tyre Punc/Air',
            7 => 'Labor',
            8 => 'Repair',
            9 => 'Misc',
        ];

        $report = [];

        // Grand total initialize
        $grandTotal = [
            'trips' => 0,
            'Meal' => 0,
            'Fueling' => 0,
            'Service' => 0,
            'Route' => 0,
            'Toll Tax' => 0,
            'Tyre Punc/Air' => 0,
            'Labor' => 0,
            'Repair' => 0,
            'Misc' => 0,
        ];

        foreach ($trips as $trip) {

            // Safety checks
            if (!$trip->vehicle || !$trip->vehicle->new_wheeler) {
                continue;
            }

            $category  = $trip->vehicle->new_wheeler->name;
            $vehicleNo = $trip->vehicle->vehicle_no;

            // Initialize vehicle row
            if (!isset($report[$category][$vehicleNo])) {
                $report[$category][$vehicleNo] = [
                    'trips' => 0,
                    'Meal' => 0,
                    'Fueling' => 0,
                    'Service' => 0,
                    'Route' => 0,
                    'Toll Tax' => 0,
                    'Tyre Punc/Air' => 0,
                    'Labor' => 0,
                    'Repair' => 0,
                    'Misc' => 0,
                ];
            }

            // Count trips
            $report[$category][$vehicleNo]['trips']++;
            $grandTotal['trips']++;

            // Expenses
            foreach ($trip->tripExpenses as $expense) {

                if (!isset($expenseMap[$expense->expense])) {
                    continue;
                }

                $key = $expenseMap[$expense->expense];

                $report[$category][$vehicleNo][$key] += (float) $expense->amount;
                $grandTotal[$key] += (float) $expense->amount;
            }
        }

        return view('admin.reports.vehicle__summary_report', compact('report', 'grandTotal'));
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
                                if ($request->date_range) {
                                    $dates = explode(' - ', $request->date_range);

                                    $start = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->format('Y-m-d');
                                    $end   = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->format('Y-m-d');

                                    $query->whereBetween('start_date', [$start, $end]);
                                }


                                // if ($request->start_date && $request->end_date) {
                                //     $query->whereBetween('start_date', [$request->start_date, $request->end_date]);
                                // }

                                // Filter: multi date range
                                // if ($request->start_date) {
                                //     $query->where('start_date', $request->start_date);
                                // }
                                $reports = $query->orderByDesc('id')->paginate(50);

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
        $start = "";
        $end = "";

        $query = TripDetail::with('trip.vehicle', 'customer')
            ->join('trips', 'trip_details.trip_id', '=', 'trips.id') // JOIN added
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
                if ($request->date_range) {
                    $dates = explode(' - ', $request->date_range);

                    $start = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->format('Y-m-d');
                    $end   = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->format('Y-m-d');

                    $query->whereBetween('start_date', [$start, $end]);
                }

            $reports = $query->groupBy('trips.vehicle_id')->paginate(50);
            // ->orderByDesc('trip_id')
            

        return view('admin.reports.view_weekly_labour', compact('reports', "start", "end"));
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
                            if ($request->date_range) {
                                $dates = explode(' - ', $request->date_range);

                                $start = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->format('Y-m-d');
                                $end   = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->format('Y-m-d');

                                $query->whereBetween('start_date', [$start, $end]);
                            }
                            
                            $reports = $query->orderByDesc('id')->paginate(50);
        return view('admin.reports.baloch_labour', compact('reports'));
    }

    public function view_baloch_labour_report(Request $request)
    {

        $start = "";
        $end   = "";

        $query = TripDetail::with('trip.vehicle', 'customer')
            ->join('trips', 'trip_details.trip_id', '=', 'trips.id')
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
            ->where('baloch_labour', '!=', '');
            if ($request->trip_no) {
                $query->where('trip_id', $request->trip_no);
            }
            // Filter: vehicle no
            if ($request->vehicle_no) {
                $query->whereHas('trip.vehicle', function($q) use ($request) {
                $q->where('vehicle_no', 'LIKE', '%'.$request->vehicle_no.'%');
                });
            }
            if ($request->date_range) {
                $dates = explode(' - ', $request->date_range);

                $start = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->format('Y-m-d');
                $end   = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->format('Y-m-d');

                $query->whereBetween('start_date', [$start, $end]);
            }
            $reports = $query->groupBy('trips.vehicle_id')->paginate(50);

        return view('admin.reports.view_baloch_labour', compact('reports', 'start', 'end'));
    }
}

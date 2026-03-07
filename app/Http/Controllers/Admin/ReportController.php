<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Trip,
    TripPayment,
    TripVehicleExpense,
    Vehicle,
    Diesel,
    Driver,
    ExpenseCategory,
    TripDetail,
    Maintenance,
    Issuance,
    Inventory,
    Overhead,
    InventoryItem,
    ExpenseType
};

use Carbon\Carbon;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;


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
        $trip = Trip::with(['fuelings', 'vehicle', 'driver', 'tripDetails', 'tripPayments', 'tripExpenses', 'tripExpenses.expenseName'])->findOrFail($tripId);
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

    // public function vehicleSummaryReport(Request $request)
    // {
    //     // Date range set karein
    //     $fromDate = $request->filled('from_date') 
    //         ? Carbon::parse($request->from_date)->startOfDay()
    //         : Carbon::today()->startOfDay();
        
    //     $toDate = $request->filled('to_date')
    //         ? Carbon::parse($request->to_date)->endOfDay()
    //         : Carbon::today()->endOfDay();

    //     // Calculate total days in date range
    //     $totalDays = $fromDate->diffInDays($toDate) + 1;

    //     // Get all expense categories dynamically from database
    //     $expenseCategories = ExpenseCategory::orderBy('id')->get();
        
    //     // Create dynamic category array for initialization
    //     $expenseCategoryKeys = [];
    //     foreach ($expenseCategories as $category) {
    //         $expenseCategoryKeys[$category->name] = 0;
    //     }

    //     // Trips fetch karein with proper relationships
    //     $trips = Trip::with(['vehicle.new_wheeler', 'tripExpenses.expenseType.category', 'tripDetails'])
    //                     ->whereBetween('trip_end_date', [$fromDate, $toDate])
    //                     ->get();

    //     $diesels = Diesel::with('vehicle.new_wheeler')
    //                 ->whereBetween('date', [$fromDate, $toDate])
    //                 ->get();

    //     // Drivers fetch karein with vehicle relationship
    //     $drivers = Driver::with('vehicle')->where('status', 'active')->get();

    //     // Fetch TripPayments (Advance payments) for date range
    //     $tripPayments = TripPayment::with(['trip.vehicle.new_wheeler'])
    //                     ->whereBetween('date', [$fromDate, $toDate])
    //                     ->get();

    //     // Fetch TripVehicleExpense where expense_from = "From Advance Amount"
    //     $advanceExpenses = TripVehicleExpense::with(['vehicle.new_wheeler', 'trip', 'expenseType.category'])
    //                         ->where('expense_from', 'From Advance Amount')
    //                         ->whereHas('trip', function($query) use ($fromDate, $toDate) {
    //                             $query->whereBetween('trip_end_date', [$fromDate, $toDate]);
    //                         })
    //                         ->get();

    //     $report = [];
        
    //     // Grand total initialize - with dynamic expense categories
    //     $grandTotal = array_merge([
    //         'trips'         => 0,
    //         'total_journeys' => 0,
    //     ], $expenseCategoryKeys, [
    //         'Advance'       => 0,
    //         'Salary'        => 0,
    //         'Total_Exp'     => 0,
    //         'Sale_Rent'     => 0,
    //         'Gross_Earning' => 0,
    //         'Net_Earning'   => 0
    //     ]);

    //     // Category-wise totals initialize
    //     $categoryTotals = [];

    //     foreach ($trips as $trip) {
    //         // Safety checks
    //         if (!$trip->vehicle || !$trip->vehicle->new_wheeler) {
    //             continue;
    //         }

    //         $category  = $trip->vehicle->new_wheeler->name;
    //         $vehicleNo = $trip->vehicle->vehicle_no;

    //         // Initialize category total agar pehle se nahi hai
    //         if (!isset($categoryTotals[$category])) {
    //             $categoryTotals[$category] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         // Initialize vehicle row agar pehle se nahi hai
    //         if (!isset($report[$category][$vehicleNo])) {
    //             $report[$category][$vehicleNo] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         // Trip count
    //         $report[$category][$vehicleNo]['trips']++;
    //         $categoryTotals[$category]['trips']++;
    //         $grandTotal['trips']++;

    //         // Journey count (tripDetails ka count)
    //         $journeyCount = $trip->tripDetails->count();
    //         $report[$category][$vehicleNo]['total_journeys'] += $journeyCount;
    //         $categoryTotals[$category]['total_journeys'] += $journeyCount;
    //         $grandTotal['total_journeys'] += $journeyCount;

    //         // Expenses calculate karein - GROUP BY CATEGORY
    //         $tripTotalExpense = 0;
            
    //         foreach ($trip->tripExpenses as $expense) {
    //             $amount = (float) ($expense->amount ?? 0);
    //             $tripTotalExpense += $amount;
                
    //             // Get category name from expense type relationship
    //             if ($expense->expenseType && $expense->expenseType->category) {
    //                 $categoryName = $expense->expenseType->category->name;
                    
    //                 // Add to respective CATEGORY column (not expense type)
    //                 if (isset($report[$category][$vehicleNo][$categoryName])) {
    //                     $report[$category][$vehicleNo][$categoryName] += $amount;
    //                     $categoryTotals[$category][$categoryName] += $amount;
    //                     $grandTotal[$categoryName] += $amount;
    //                 }
    //             }
    //         }

    //         // Total Expense add karo
    //         $report[$category][$vehicleNo]['Total_Exp'] += $tripTotalExpense;
    //         $categoryTotals[$category]['Total_Exp'] += $tripTotalExpense;
    //         $grandTotal['Total_Exp'] += $tripTotalExpense;

    //         // Trip details se rent calculate karein
    //         foreach ($trip->tripDetails as $detail) {
    //             $rent = (float) ($detail->rent ?? 0);
    //             $report[$category][$vehicleNo]['Sale_Rent'] += $rent;
    //             $categoryTotals[$category]['Sale_Rent'] += $rent;
    //             $grandTotal['Sale_Rent'] += $rent;
    //         }
    //     }

    //     // Diesel entries process karein
    //     foreach ($diesels as $diesel) {
    //         // Safety checks
    //         if (!$diesel->vehicle || !$diesel->vehicle->new_wheeler) {
    //             continue;
    //         }

    //         $category  = $diesel->vehicle->new_wheeler->name;
    //         $vehicleNo = $diesel->vehicle->vehicle_no;

    //         // Initialize category total agar pehle se nahi hai
    //         if (!isset($categoryTotals[$category])) {
    //             $categoryTotals[$category] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         // Agar vehicle pehle se report mein nahi hai, initialize karo
    //         if (!isset($report[$category][$vehicleNo])) {
    //             $report[$category][$vehicleNo] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         // Fueling amount add karo
    //         $fuelingAmount = (float) ($diesel->total_amount ?? 0);
            
    //         // Find "Fuel" category dynamically from database
    //         $fuelCategory = $expenseCategories->where('name', 'Fuel')->first();
    //         if ($fuelCategory) {
    //             $report[$category][$vehicleNo][$fuelCategory->name] += $fuelingAmount;
    //             $categoryTotals[$category][$fuelCategory->name] += $fuelingAmount;
    //             $grandTotal[$fuelCategory->name] += $fuelingAmount;
    //         }

    //         // Diesel amount ko Total_Exp mein bhi add karo
    //         $report[$category][$vehicleNo]['Total_Exp'] += $fuelingAmount;
    //         $categoryTotals[$category]['Total_Exp'] += $fuelingAmount;
    //         $grandTotal['Total_Exp'] += $fuelingAmount;
    //     }

    //     // Calculate Advance: TripPayments - Expenses from Advance
    //     foreach ($tripPayments as $payment) {
    //         if (!$payment->trip || !$payment->trip->vehicle || !$payment->trip->vehicle->new_wheeler) {
    //             continue;
    //         }

    //         $category = $payment->trip->vehicle->new_wheeler->name;
    //         $vehicleNo = $payment->trip->vehicle->vehicle_no;

    //         // Initialize agar vehicle report mein nahi hai
    //         if (!isset($categoryTotals[$category])) {
    //             $categoryTotals[$category] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         if (!isset($report[$category][$vehicleNo])) {
    //             $report[$category][$vehicleNo] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         $paymentAmount = (float) ($payment->amount ?? 0);
    //         $report[$category][$vehicleNo]['Advance'] += $paymentAmount;
    //         $categoryTotals[$category]['Advance'] += $paymentAmount;
    //         $grandTotal['Advance'] += $paymentAmount;
    //     }

    //     // Subtract expenses paid from advance
    //     foreach ($advanceExpenses as $advExp) {
    //         if (!$advExp->vehicle || !$advExp->vehicle->new_wheeler) {
    //             continue;
    //         }

    //         $category = $advExp->vehicle->new_wheeler->name;
    //         $vehicleNo = $advExp->vehicle->vehicle_no;

    //         if (isset($report[$category][$vehicleNo])) {
    //             $expenseAmount = (float) ($advExp->amount ?? 0);
                
    //             // ONLY subtract from Advance, DO NOT add to Total_Exp
    //             $report[$category][$vehicleNo]['Advance'] -= $expenseAmount;
    //             $categoryTotals[$category]['Advance'] -= $expenseAmount;
    //             $grandTotal['Advance'] -= $expenseAmount;
    //         }
    //     }

    //     // Calculate salary for each vehicle
    //     foreach ($drivers as $driver) {
    //         if (!$driver->vehicle || !$driver->vehicle->new_wheeler) {
    //             continue;
    //         }

    //         $category = $driver->vehicle->new_wheeler->name;
    //         $vehicleNo = $driver->vehicle->vehicle_no;

    //         $monthlySalary = (float) ($driver->salary ?? 0);
    //         $dailySalary = $monthlySalary / 30;
    //         $totalSalary = $dailySalary * $totalDays;

    //         if (isset($report[$category][$vehicleNo])) {
    //             // Find "Salaries" category from database
    //             $salariesCategory = $expenseCategories->where('name', 'Salaries')->first();
                
    //             if ($salariesCategory) {
    //                 // Add to Salaries CATEGORY column
    //                 $report[$category][$vehicleNo][$salariesCategory->name] += $totalSalary;
    //                 $categoryTotals[$category][$salariesCategory->name] += $totalSalary;
    //                 $grandTotal[$salariesCategory->name] += $totalSalary;
    //             }
                
    //             // Also keep in Salary for second table
    //             $report[$category][$vehicleNo]['Salary'] += $totalSalary;
    //             $categoryTotals[$category]['Salary'] += $totalSalary;
    //             $grandTotal['Salary'] += $totalSalary;

    //             // Salary ko Total_Exp mein bhi add karo
    //             $report[$category][$vehicleNo]['Total_Exp'] += $totalSalary;
    //             $categoryTotals[$category]['Total_Exp'] += $totalSalary;
    //             $grandTotal['Total_Exp'] += $totalSalary;
    //         }
    //     }

    //     // Calculate Gross Earning and Net Earning
    //     foreach ($report as $category => $vehicles) {
    //         foreach ($vehicles as $vehicleNo => $data) {
    //             $grossEarning = $data['Sale_Rent'];
    //             $report[$category][$vehicleNo]['Gross_Earning'] = $grossEarning;
    //             $categoryTotals[$category]['Gross_Earning'] += $grossEarning;
    //             $grandTotal['Gross_Earning'] += $grossEarning;

    //             $netEarning = $grossEarning - $data['Total_Exp'];
    //             $report[$category][$vehicleNo]['Net_Earning'] = $netEarning;
    //             $categoryTotals[$category]['Net_Earning'] += $netEarning;
    //             $grandTotal['Net_Earning'] += $netEarning;
    //         }
    //     }

    //     return view('admin.reports.vehicle__summary_report', compact(
    //         'report', 
    //         'categoryTotals', 
    //         'grandTotal', 
    //         'fromDate', 
    //         'toDate',
    //         'expenseCategories'
    //     ));
    // }

    // LATEST
    // public function vehicleSummaryReport(Request $request)
    // {
    //     // Date range set karein
    //     $fromDate = $request->filled('from_date') 
    //         ? Carbon::parse($request->from_date)->startOfDay()
    //         : Carbon::today()->startOfDay();
        
    //     $toDate = $request->filled('to_date')
    //         ? Carbon::parse($request->to_date)->endOfDay()
    //         : Carbon::today()->endOfDay();

    //     // Calculate total days in date range
    //     $totalDays = $fromDate->diffInDays($toDate) + 1;

    //     // Get all expense categories dynamically from database
    //     $expenseCategories = ExpenseCategory::where("name", '!=', 'Overheads')->orderBy('id')->get();
        
    //     // Create dynamic category array for initialization
    //     $expenseCategoryKeys = [];
    //     foreach ($expenseCategories as $category) {
    //         $expenseCategoryKeys[$category->name] = 0;
    //     }

    //     // Trips fetch karein with proper relationships - EXCLUDE TRAILERS (vehicle_type = 2)
    //     $trips = Trip::with(['vehicle.new_wheeler', 'tripExpenses.expenseType.category', 'tripDetails'])
    //                     ->whereBetween('trip_end_date', [$fromDate, $toDate])
    //                     ->whereHas('vehicle', function($query) {
    //                         $query->where('vehicle_type', '!=', 2); // Exclude trailers
    //                     })
    //                     ->get();

    //     $diesels = Diesel::with('vehicle.new_wheeler')
    //                 ->whereBetween('date', [$fromDate, $toDate])
    //                 ->whereHas('vehicle', function($query) {
    //                     $query->where('vehicle_type', '!=', 2); // Exclude trailers
    //                 })
    //                 ->get();

    //     // Drivers fetch karein with vehicle relationship - EXCLUDE TRAILERS
    //     $drivers = Driver::with('vehicle')
    //                 ->where('status', 'active')
    //                 ->whereHas('vehicle', function($query) {
    //                     $query->where('vehicle_type', '!=', 2); // Exclude trailers
    //                 })
    //                 ->get();

    //     // Fetch TripPayments (Advance payments) for date range - EXCLUDE TRAILERS
    //     $tripPayments = TripPayment::with(['trip.vehicle.new_wheeler'])
    //                     ->whereBetween('date', [$fromDate, $toDate])
    //                     ->whereHas('trip.vehicle', function($query) {
    //                         $query->where('vehicle_type', '!=', 2); // Exclude trailers
    //                     })
    //                     ->get();

    //     // Fetch TripVehicleExpense where expense_from = "From Advance Amount" - EXCLUDE TRAILERS
    //     $advanceExpenses = TripVehicleExpense::with(['vehicle.new_wheeler', 'trip', 'expenseType.category'])
    //                         ->where('expense_from', 'From Advance Amount')
    //                         ->whereHas('vehicle', function($query) {
    //                             $query->where('vehicle_type', '!=', 2); // Exclude trailers
    //                         })
    //                         ->whereHas('trip', function($query) use ($fromDate, $toDate) {
    //                             $query->whereBetween('trip_end_date', [$fromDate, $toDate]);
    //                         })
    //                         ->get();

    //     $report = [];
        
    //     // Grand total initialize - with dynamic expense categories
    //     $grandTotal = array_merge([
    //         'trips'         => 0,
    //         'total_journeys' => 0,
    //     ], $expenseCategoryKeys, [
    //         'Advance'       => 0,
    //         'Salary'        => 0,
    //         'Maintenance & Workshop'   => 0,
    //         'Total_Exp'     => 0,
    //         'Sale_Rent'     => 0,
    //         'Gross_Earning' => 0,
    //         'Net_Earning'   => 0
    //     ]);

    //     // Category-wise totals initialize
    //     $categoryTotals = [];

    //     foreach ($trips as $trip) {
    //         // Safety checks
    //         if (!$trip->vehicle || !$trip->vehicle->new_wheeler) {
    //             continue;
    //         }

    //         $category  = $trip->vehicle->new_wheeler->name;
    //         $vehicleNo = $trip->vehicle->vehicle_no;

    //         // Initialize category total agar pehle se nahi hai
    //         if (!isset($categoryTotals[$category])) {
    //             $categoryTotals[$category] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         // Initialize vehicle row agar pehle se nahi hai
    //         if (!isset($report[$category][$vehicleNo])) {
    //             $report[$category][$vehicleNo] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Maintenance & Workshop' => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         // Trip count
    //         $report[$category][$vehicleNo]['trips']++;
    //         $categoryTotals[$category]['trips']++;
    //         $grandTotal['trips']++;

    //         // Journey count (tripDetails ka count)
    //         $journeyCount = $trip->tripDetails->count();
    //         $report[$category][$vehicleNo]['total_journeys'] += $journeyCount;
    //         $categoryTotals[$category]['total_journeys']     += $journeyCount;
    //         $grandTotal['total_journeys']                    += $journeyCount;

    //         // Expenses calculate karein - GROUP BY CATEGORY
    //         $tripTotalExpense = 0;
            
    //         foreach ($trip->tripExpenses as $expense) {
    //             $amount = (float) ($expense->amount ?? 0);
    //             $tripTotalExpense += $amount;
                
    //             // Get category name from expense type relationship
    //             if ($expense->expenseType && $expense->expenseType->category) {
    //                 $categoryName = $expense->expenseType->category->name;
                    
    //                 // Add to respective CATEGORY column (not expense type)
    //                 if (isset($report[$category][$vehicleNo][$categoryName])) {
    //                     $report[$category][$vehicleNo][$categoryName] += $amount;
    //                     $categoryTotals[$category][$categoryName] += $amount;
    //                     $grandTotal[$categoryName] += $amount;
    //                 }
    //             }
    //         }

    //         // Total Expense add karo
    //         $report[$category][$vehicleNo]['Total_Exp'] += $tripTotalExpense;
    //         $categoryTotals[$category]['Total_Exp'] += $tripTotalExpense;
    //         $grandTotal['Total_Exp'] += $tripTotalExpense;

    //         // Trip details se rent calculate karein
    //         foreach ($trip->tripDetails as $detail) {
    //             $rent = (float) ($detail->rent ?? 0);
    //             $report[$category][$vehicleNo]['Sale_Rent'] += $rent;
    //             $categoryTotals[$category]['Sale_Rent'] += $rent;
    //             $grandTotal['Sale_Rent'] += $rent;
    //         }

    //         // Trip balance ko Advance mein add/subtract karein
    //         // Positive balance = add to Advance
    //         // Negative balance = subtract from Advance
    //         $tripBalance = (float) ($trip->balance ?? 0);
            
    //         $report[$category][$vehicleNo]['Advance'] += $tripBalance;
    //         $categoryTotals[$category]['Advance'] += $tripBalance;
    //         $grandTotal['Advance'] += $tripBalance;
    //     }

    //     // Diesel entries process karein
    //     foreach ($diesels as $diesel) {
    //         // Safety checks
    //         if (!$diesel->vehicle || !$diesel->vehicle->new_wheeler) {
    //             continue;
    //         }

    //         $category  = $diesel->vehicle->new_wheeler->name;
    //         $vehicleNo = $diesel->vehicle->vehicle_no;

    //         // Initialize category total agar pehle se nahi hai
    //         if (!isset($categoryTotals[$category])) {
    //             $categoryTotals[$category] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Maintenance & Workshop' => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         // Agar vehicle pehle se report mein nahi hai, initialize karo
    //         if (!isset($report[$category][$vehicleNo])) {
    //             $report[$category][$vehicleNo] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         // Fueling amount add karo
    //         $fuelingAmount = (float) ($diesel->total_amount ?? 0);
            
    //         // Find "Fuel" category dynamically from database
    //         $fuelCategory = $expenseCategories->where('name', 'Fuel')->first();
    //         if ($fuelCategory) {
    //             $report[$category][$vehicleNo][$fuelCategory->name] += $fuelingAmount;
    //             $categoryTotals[$category][$fuelCategory->name] += $fuelingAmount;
    //             $grandTotal[$fuelCategory->name] += $fuelingAmount;
    //         }

    //         // Diesel amount ko Total_Exp mein bhi add karo
    //         $report[$category][$vehicleNo]['Total_Exp'] += $fuelingAmount;
    //         $categoryTotals[$category]['Total_Exp'] += $fuelingAmount;
    //         $grandTotal['Total_Exp'] += $fuelingAmount;
    //     }

    //     // Calculate Advance: TripPayments - Expenses from Advance
    //     foreach ($tripPayments as $payment) {
    //         if (!$payment->trip || !$payment->trip->vehicle || !$payment->trip->vehicle->new_wheeler) {
    //             continue;
    //         }

    //         $category = $payment->trip->vehicle->new_wheeler->name;
    //         $vehicleNo = $payment->trip->vehicle->vehicle_no;

    //         // Initialize agar vehicle report mein nahi hai
    //         if (!isset($categoryTotals[$category])) {
    //             $categoryTotals[$category] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         if (!isset($report[$category][$vehicleNo])) {
    //             $report[$category][$vehicleNo] = array_merge([
    //                 'trips'         => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0
    //             ]);
    //         }

    //         $paymentAmount = (float) ($payment->amount ?? 0);
    //         $report[$category][$vehicleNo]['Advance'] += $paymentAmount;
    //         $categoryTotals[$category]['Advance'] += $paymentAmount;
    //         $grandTotal['Advance'] += $paymentAmount;
    //     }

    //     // Subtract expenses paid from advance
    //     foreach ($advanceExpenses as $advExp) {
    //         if (!$advExp->vehicle || !$advExp->vehicle->new_wheeler) {
    //             continue;
    //         }

    //         $category = $advExp->vehicle->new_wheeler->name;
    //         $vehicleNo = $advExp->vehicle->vehicle_no;

    //         if (isset($report[$category][$vehicleNo])) {
    //             $expenseAmount = (float) ($advExp->amount ?? 0);
                
    //             // ONLY subtract from Advance, DO NOT add to Total_Exp
    //             $report[$category][$vehicleNo]['Advance'] -= $expenseAmount;
    //             $categoryTotals[$category]['Advance'] -= $expenseAmount;
    //             $grandTotal['Advance'] -= $expenseAmount;
    //         }
    //     }

    //     // Calculate salary for each vehicle
    //     foreach ($drivers as $driver) {
    //         if (!$driver->vehicle || !$driver->vehicle->new_wheeler) {
    //             continue;
    //         }

    //         $category = $driver->vehicle->new_wheeler->name;
    //         $vehicleNo = $driver->vehicle->vehicle_no;

    //         $monthlySalary = (float) ($driver->salary ?? 0);
    //         $dailySalary = $monthlySalary / 30;
    //         $totalSalary = $dailySalary * $totalDays;

    //         if (isset($report[$category][$vehicleNo])) {
    //             // Find "Salaries" category from database
    //             $salariesCategory = $expenseCategories->where('name', 'Salaries')->first();
                
    //             if ($salariesCategory) {
    //                 // Add to Salaries CATEGORY column
    //                 $report[$category][$vehicleNo][$salariesCategory->name] += $totalSalary;
    //                 $categoryTotals[$category][$salariesCategory->name] += $totalSalary;
    //                 $grandTotal[$salariesCategory->name] += $totalSalary;
    //             }
                
    //             // Also keep in Salary for second table
    //             $report[$category][$vehicleNo]['Salary'] += $totalSalary;
    //             $categoryTotals[$category]['Salary'] += $totalSalary;
    //             $grandTotal['Salary'] += $totalSalary;

    //             // Salary ko Total_Exp mein bhi add karo
    //             $report[$category][$vehicleNo]['Total_Exp'] += $totalSalary;
    //             $categoryTotals[$category]['Total_Exp'] += $totalSalary;
    //             $grandTotal['Total_Exp'] += $totalSalary;
    //         }
    //     }

    //     // Calculate Gross Earning and Net Earning
    //     foreach ($report as $category => $vehicles) {
    //         foreach ($vehicles as $vehicleNo => $data) {
    //             $grossEarning = $data['Sale_Rent'];
    //             $report[$category][$vehicleNo]['Gross_Earning'] = $grossEarning;
    //             $categoryTotals[$category]['Gross_Earning'] += $grossEarning;
    //             $grandTotal['Gross_Earning'] += $grossEarning;

    //             $netEarning = $grossEarning - $data['Total_Exp'];
    //             $report[$category][$vehicleNo]['Net_Earning'] = $netEarning;
    //             $categoryTotals[$category]['Net_Earning'] += $netEarning;
    //             $grandTotal['Net_Earning'] += $netEarning;
    //         }
    //     }

    //     return view('admin.reports.vehicle__summary_report', compact(
    //         'report', 
    //         'categoryTotals', 
    //         'grandTotal', 
    //         'fromDate', 
    //         'toDate',
    //         'expenseCategories'
    //     ));
    // }

    // Complete vehicleSummaryReport() method — Maintenance Workshop column included

    // public function vehicleSummaryReport(Request $request)
    // {
    //     try {
    //         // Date range set karein
    //         $fromDate = $request->filled('from_date')
    //             ? Carbon::parse($request->from_date)->startOfDay()
    //             : Carbon::today()->startOfDay();

    //         $toDate = $request->filled('to_date')
    //             ? Carbon::parse($request->to_date)->endOfDay()
    //             : Carbon::today()->endOfDay();

    //         // Calculate total days in date range
    //         $totalDays = $fromDate->diffInDays($toDate) + 1;

    //         // Get all expense categories dynamically from database
    //         $expenseCategories = ExpenseCategory::where("name", '!=', 'Overheads')->orderBy('id')->get();

    //         // Create dynamic category array for initialization
    //         $expenseCategoryKeys = [];
    //         foreach ($expenseCategories as $category) {
    //             $expenseCategoryKeys[$category->name] = 0;
    //         }

    //         // Trips fetch karein with proper relationships - EXCLUDE TRAILERS (vehicle_type = 2)
    //         $trips = Trip::with(['vehicle.new_wheeler', 'tripExpenses.expenseType.category', 'tripDetails'])
    //                         ->whereBetween('trip_end_date', [$fromDate, $toDate])
    //                         ->whereHas('vehicle', function ($query) {
    //                             $query->where('vehicle_type', '!=', 2);
    //                         })
    //                         ->get();

    //         $diesels = Diesel::with('vehicle.new_wheeler')
    //                     ->whereBetween('date', [$fromDate, $toDate])
    //                     ->whereHas('vehicle', function ($query) {
    //                         $query->where('vehicle_type', '!=', 2);
    //                     })
    //                     ->get();

    //         // ---- Maintenance fetch ----
    //         $maintenances = Maintenance::with(['vehicle.new_wheeler'])
    //                                     ->whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()])
    //                                     ->whereHas('vehicle', function ($query) {
    //                                         $query->where('vehicle_type', '!=', 2);
    //                                     })
    //                                     ->get();

    //         // ---- Issuances fetch ----
    //         $issuances = Issuance::with(['vehicle.new_wheeler', 'inventory'])
    //                                 ->whereBetween('issue_date', [$fromDate->toDateString(), $toDate->toDateString()])
    //                                 ->whereHas('vehicle', function ($query) {
    //                                     $query->where('vehicle_type', '!=', 2);
    //                                 })
    //                                 ->get();

    //         // $inventories = Inventory::whereBetween('purchase_date', [$fromDate->toDateString(), $toDate->toDateString()])
    //         //                         ->sum("price");

    //         // $overheads = Overhead::whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()])->sum("amount");


    //         // Drivers fetch karein with vehicle relationship - EXCLUDE TRAILERS
    //         $drivers = Driver::with('vehicle')
    //                     ->where('status', 'active')
    //                     ->whereHas('vehicle', function ($query) {
    //                         $query->where('vehicle_type', '!=', 2);
    //                     })
    //                     ->get();

    //         // Fetch TripPayments (Advance payments) for date range - EXCLUDE TRAILERS
    //         $tripPayments = TripPayment::with(['trip.vehicle.new_wheeler'])
    //                         ->whereBetween('date', [$fromDate, $toDate])
    //                         ->whereHas('trip.vehicle', function ($query) {
    //                             $query->where('vehicle_type', '!=', 2);
    //                         })
    //                         ->get();

    //         // Fetch TripVehicleExpense where expense_from = "From Advance Amount" - EXCLUDE TRAILERS
    //         $advanceExpenses = TripVehicleExpense::with(['vehicle.new_wheeler', 'trip', 'expenseType.category'])
    //                             ->where('expense_from', 'From Advance Amount')
    //                             ->whereHas('vehicle', function ($query) {
    //                                 $query->where('vehicle_type', '!=', 2);
    //                             })
    //                             ->whereHas('trip', function ($query) use ($fromDate, $toDate) {
    //                                 $query->whereBetween('trip_end_date', [$fromDate, $toDate]);
    //                             })
    //                             ->get();

    //         $report = [];

    //         // ---- Helper closure to build a fresh row ----
    //         $blankRow = function () use ($expenseCategoryKeys) {
    //             return array_merge([
    //                 'trips'          => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Maintenance'   => 0,
    //                 'Inventory'     => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0,
    //             ]);
    //         };

    //         // Grand total initialize
    //         $grandTotal    = $blankRow();
    //         $categoryTotals = [];

    //         // ============================================================
    //         // TRIPS LOOP
    //         // ============================================================
    //         foreach ($trips as $trip) {
    //             if (!$trip->vehicle || !$trip->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category  = $trip->vehicle->new_wheeler->name;
    //             $vehicleNo = $trip->vehicle->vehicle_no;

    //             if (!isset($categoryTotals[$category])) {
    //                 $categoryTotals[$category] = $blankRow();
    //             }

    //             if (!isset($report[$category][$vehicleNo])) {
    //                 $report[$category][$vehicleNo] = $blankRow();
    //             }

    //             // Trip count
    //             $report[$category][$vehicleNo]['trips']++;
    //             $categoryTotals[$category]['trips']++;
    //             $grandTotal['trips']++;

    //             // Journey count
    //             $journeyCount = $trip->tripDetails->count();
    //             $report[$category][$vehicleNo]['total_journeys'] += $journeyCount;
    //             $categoryTotals[$category]['total_journeys']     += $journeyCount;
    //             $grandTotal['total_journeys']                    += $journeyCount;

    //             // Expenses grouped by category
    //             $tripTotalExpense = 0;

    //             foreach ($trip->tripExpenses as $expense) {
    //                 $amount = (float) ($expense->amount ?? 0);
    //                 $tripTotalExpense += $amount;

    //                 if ($expense->expenseType && $expense->expenseType->category) {
    //                     $categoryName = $expense->expenseType->category->name;

    //                     if (isset($report[$category][$vehicleNo][$categoryName])) {
    //                         $report[$category][$vehicleNo][$categoryName] += $amount;
    //                         $categoryTotals[$category][$categoryName]     += $amount;
    //                         $grandTotal[$categoryName]                    += $amount;
    //                     }
    //                 }
    //             }

    //             // Total Expense
    //             $report[$category][$vehicleNo]['Total_Exp'] += $tripTotalExpense;
    //             $categoryTotals[$category]['Total_Exp']     += $tripTotalExpense;
    //             $grandTotal['Total_Exp']                    += $tripTotalExpense;

    //             // Sale / Rent from trip details
    //             foreach ($trip->tripDetails as $detail) {
    //                 $rent = (float) ($detail->rent ?? 0);
    //                 $report[$category][$vehicleNo]['Sale_Rent'] += $rent;
    //                 $categoryTotals[$category]['Sale_Rent']     += $rent;
    //                 $grandTotal['Sale_Rent']                    += $rent;
    //             }

    //             // Trip balance → Advance
    //             $tripBalance = (float) ($trip->balance ?? 0);
    //             $report[$category][$vehicleNo]['Advance'] += $tripBalance;
    //             $categoryTotals[$category]['Advance']     += $tripBalance;
    //             $grandTotal['Advance']                    += $tripBalance;
    //         }

    //         // ============================================================
    //         // DIESEL LOOP
    //         // ============================================================
    //         foreach ($diesels as $diesel) {
    //             if (!$diesel->vehicle || !$diesel->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category  = $diesel->vehicle->new_wheeler->name;
    //             $vehicleNo = $diesel->vehicle->vehicle_no;

    //             if (!isset($categoryTotals[$category])) {
    //                 $categoryTotals[$category] = $blankRow();
    //             }

    //             if (!isset($report[$category][$vehicleNo])) {
    //                 $report[$category][$vehicleNo] = $blankRow();
    //             }

    //             $fuelingAmount = (float) ($diesel->total_amount ?? 0);

    //             $fuelCategory = $expenseCategories->where('name', 'Fuel')->first();
    //             if ($fuelCategory) {
    //                 $report[$category][$vehicleNo][$fuelCategory->name] += $fuelingAmount;
    //                 $categoryTotals[$category][$fuelCategory->name]     += $fuelingAmount;
    //                 $grandTotal[$fuelCategory->name]                    += $fuelingAmount;
    //             }

    //             $report[$category][$vehicleNo]['Total_Exp'] += $fuelingAmount;
    //             $categoryTotals[$category]['Total_Exp']     += $fuelingAmount;
    //             $grandTotal['Total_Exp']                    += $fuelingAmount;
    //         }

    //         // ============================================================
    //         // MAINTENANCE LOOP
    //         // ============================================================
    //         foreach ($maintenances as $maintenance) {
    //             if (!$maintenance->vehicle || !$maintenance->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category  = $maintenance->vehicle->new_wheeler->name;
    //             $vehicleNo = $maintenance->vehicle->vehicle_no;

    //             if (!isset($categoryTotals[$category])) {
    //                 $categoryTotals[$category] = $blankRow();
    //             }

    //             if (!isset($report[$category][$vehicleNo])) {
    //                 $report[$category][$vehicleNo] = $blankRow();
    //             }

    //             $maintenanceAmount = (float) ($maintenance->amount ?? 0);

    //             // Maintenance column
    //             $report[$category][$vehicleNo]['Maintenance'] += $maintenanceAmount;
    //             $categoryTotals[$category]['Maintenance']     += $maintenanceAmount;
    //             $grandTotal['Maintenance']                    += $maintenanceAmount;

    //             // Total_Exp mein bhi add
    //             $report[$category][$vehicleNo]['Total_Exp'] += $maintenanceAmount;
    //             $categoryTotals[$category]['Total_Exp']     += $maintenanceAmount;
    //             $grandTotal['Total_Exp']                    += $maintenanceAmount;
    //         }

    //         // ============================================================
    //         // ISSUANCE LOOP
    //         // ============================================================
    //         foreach ($issuances as $issuance) {
    //             if (!$issuance->vehicle || !$issuance->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category  = $issuance->vehicle->new_wheeler->name;
    //             $vehicleNo = $issuance->vehicle->vehicle_no;

    //             if (!isset($categoryTotals[$category])) {
    //                 $categoryTotals[$category] = $blankRow();
    //             }

    //             if (!isset($report[$category][$vehicleNo])) {
    //                 $report[$category][$vehicleNo] = $blankRow();
    //             }

    //             // qty * unit_price from inventory
    //             $issuanceAmount = (float) ($issuance->qty ?? 0) * (float) ($issuance->inventory->unit_price ?? 0);

    //             $report[$category][$vehicleNo]['Inventory'] += $issuanceAmount;
    //             $categoryTotals[$category]['Inventory']     += $issuanceAmount;
    //             $grandTotal['Inventory']                    += $issuanceAmount;

    //             $report[$category][$vehicleNo]['Total_Exp'] += $issuanceAmount;
    //             $categoryTotals[$category]['Total_Exp']     += $issuanceAmount;
    //             $grandTotal['Total_Exp']                    += $issuanceAmount;
    //         }

    //         // ============================================================
    //         // TRIP PAYMENTS LOOP (Advance)
    //         // ============================================================
    //         foreach ($tripPayments as $payment) {
    //             if (!$payment->trip || !$payment->trip->vehicle || !$payment->trip->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category  = $payment->trip->vehicle->new_wheeler->name;
    //             $vehicleNo = $payment->trip->vehicle->vehicle_no;

    //             if (!isset($categoryTotals[$category])) {
    //                 $categoryTotals[$category] = $blankRow();
    //             }

    //             if (!isset($report[$category][$vehicleNo])) {
    //                 $report[$category][$vehicleNo] = $blankRow();
    //             }

    //             $paymentAmount = (float) ($payment->amount ?? 0);
    //             $report[$category][$vehicleNo]['Advance'] += $paymentAmount;
    //             $categoryTotals[$category]['Advance']     += $paymentAmount;
    //             $grandTotal['Advance']                    += $paymentAmount;
    //         }

    //         // ============================================================
    //         // ADVANCE EXPENSES LOOP (subtract from Advance)
    //         // ============================================================
    //         foreach ($advanceExpenses as $advExp) {
    //             if (!$advExp->vehicle || !$advExp->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category  = $advExp->vehicle->new_wheeler->name;
    //             $vehicleNo = $advExp->vehicle->vehicle_no;

    //             if (isset($report[$category][$vehicleNo])) {
    //                 $expenseAmount = (float) ($advExp->amount ?? 0);

    //                 // ONLY subtract from Advance, NOT from Total_Exp
    //                 $report[$category][$vehicleNo]['Advance'] -= $expenseAmount;
    //                 $categoryTotals[$category]['Advance']     -= $expenseAmount;
    //                 $grandTotal['Advance']                    -= $expenseAmount;
    //             }
    //         }

    //         // ============================================================
    //         // SALARY LOOP
    //         // ============================================================
    //         foreach ($drivers as $driver) {
    //             if (!$driver->vehicle || !$driver->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category  = $driver->vehicle->new_wheeler->name;
    //             $vehicleNo = $driver->vehicle->vehicle_no;

    //             $monthlySalary = (float) ($driver->salary ?? 0);
    //             $dailySalary   = $monthlySalary / 30;
    //             $totalSalary   = $dailySalary * $totalDays;

    //             if (isset($report[$category][$vehicleNo])) {
    //                 $salariesCategory = $expenseCategories->where('name', 'Salaries')->first();

    //                 if ($salariesCategory) {
    //                     $report[$category][$vehicleNo][$salariesCategory->name] += $totalSalary;
    //                     $categoryTotals[$category][$salariesCategory->name]     += $totalSalary;
    //                     $grandTotal[$salariesCategory->name]                    += $totalSalary;
    //                 }

    //                 $report[$category][$vehicleNo]['Salary'] += $totalSalary;
    //                 $categoryTotals[$category]['Salary']     += $totalSalary;
    //                 $grandTotal['Salary']                    += $totalSalary;

    //                 $report[$category][$vehicleNo]['Total_Exp'] += $totalSalary;
    //                 $categoryTotals[$category]['Total_Exp']     += $totalSalary;
    //                 $grandTotal['Total_Exp']                    += $totalSalary;
    //             }
    //         }

    //         // ============================================================
    //         // GROSS EARNING & NET EARNING
    //         // ============================================================
    //         foreach ($report as $category => $vehicles) {
    //             foreach ($vehicles as $vehicleNo => $data) {
    //                 $grossEarning = $data['Sale_Rent'];
    //                 $report[$category][$vehicleNo]['Gross_Earning'] = $grossEarning;
    //                 $categoryTotals[$category]['Gross_Earning']    += $grossEarning;
    //                 $grandTotal['Gross_Earning']                   += $grossEarning;

    //                 $netEarning = $grossEarning - $data['Total_Exp'];
    //                 $report[$category][$vehicleNo]['Net_Earning'] = $netEarning;
    //                 $categoryTotals[$category]['Net_Earning']    += $netEarning;
    //                 $grandTotal['Net_Earning']                   += $netEarning;
    //             }
    //         }

    //         return view('admin.reports.vehicle__summary_report', compact(
    //             'report',
    //             'categoryTotals',
    //             'grandTotal',
    //             'fromDate',
    //             'toDate',
    //             'expenseCategories',
    //             // 'inventories',
    //             // 'overheads'

    //         ));
    //     } catch (\Exception $e) {
    //         //throw $th;
    //         return redirect()->back()->with('error', $e->getMessage());
    //     }
    // }

    private function getVehicleSummaryReportData(Request $request)
    {
        // Date range
        $fromDate = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : Carbon::today()->startOfDay();

        $toDate = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::today()->endOfDay();

        $totalDays = $fromDate->diffInDays($toDate) + 1;

        $expenseCategories = ExpenseCategory::where("name",'!=','Overheads')
                                            ->orderBy('id')
                                            ->get();

        $expenseCategoryKeys = [];
        foreach ($expenseCategories as $category) {
            $expenseCategoryKeys[$category->name] = 0;
        }
        // Trips fetch karein with proper relationships - EXCLUDE TRAILERS (vehicle_type = 2)
            $trips = Trip::with(['vehicle.new_wheeler', 'tripExpenses.expenseType.category', 'tripDetails'])
                            ->whereBetween('trip_end_date', [$fromDate, $toDate])
                            ->whereHas('vehicle', function ($query) {
                                $query->where('vehicle_type', '!=', 2);
                            })
                            ->get();

            $diesels = Diesel::with('vehicle.new_wheeler')
                        ->whereBetween('date', [$fromDate, $toDate])
                        ->whereHas('vehicle', function ($query) {
                            $query->where('vehicle_type', '!=', 2);
                        })
                        ->get();

            // ---- Maintenance fetch ----
            $maintenances = Maintenance::with(['vehicle.new_wheeler'])
                                        ->whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()])
                                        ->whereHas('vehicle', function ($query) {
                                            $query->where('vehicle_type', '!=', 2);
                                        })
                                        ->get();

            // ---- Issuances fetch ----
            $issuances = Issuance::with(['vehicle.new_wheeler', 'inventory'])
                                    ->whereBetween('issue_date', [$fromDate->toDateString(), $toDate->toDateString()])
                                    ->whereHas('vehicle', function ($query) {
                                        $query->where('vehicle_type', '!=', 2);
                                    })
                                    ->get();

            // $inventories = Inventory::whereBetween('purchase_date', [$fromDate->toDateString(), $toDate->toDateString()])
            //                         ->sum("price");

            // $overheads = Overhead::whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()])->sum("amount");


            // Drivers fetch karein with vehicle relationship - EXCLUDE TRAILERS
            $drivers = Driver::with('vehicle')
                        ->where('status', 'active')
                        ->whereHas('vehicle', function ($query) {
                            $query->where('vehicle_type', '!=', 2);
                        })
                        ->get();

            // Fetch TripPayments (Advance payments) for date range - EXCLUDE TRAILERS
            $tripPayments = TripPayment::with(['trip.vehicle.new_wheeler'])
                            ->whereBetween('date', [$fromDate, $toDate])
                            ->whereHas('trip.vehicle', function ($query) {
                                $query->where('vehicle_type', '!=', 2);
                            })
                            ->get();

            // Fetch TripVehicleExpense where expense_from = "From Advance Amount" - EXCLUDE TRAILERS
            $advanceExpenses = TripVehicleExpense::with(['vehicle.new_wheeler', 'trip', 'expenseType.category'])
                                ->where('expense_from', 'From Advance Amount')
                                ->whereHas('vehicle', function ($query) {
                                    $query->where('vehicle_type', '!=', 2);
                                })
                                ->whereHas('trip', function ($query) use ($fromDate, $toDate) {
                                    $query->whereBetween('trip_end_date', [$fromDate, $toDate]);
                                })
                                ->get();

            $report = [];

            // ---- Helper closure to build a fresh row ----
            $blankRow = function () use ($expenseCategoryKeys) {
                return array_merge([
                    'trips'          => 0,
                    'total_journeys' => 0,
                ], $expenseCategoryKeys, [
                    'Advance'       => 0,
                    'Salary'        => 0,
                    'Maintenance'   => 0,
                    'Inventory'     => 0,
                    'Total_Exp'     => 0,
                    'Sale_Rent'     => 0,
                    'Gross_Earning' => 0,
                    'Net_Earning'   => 0,
                ]);
            };

            // Grand total initialize
            $grandTotal    = $blankRow();
            $categoryTotals = [];

            // ============================================================
            // TRIPS LOOP
            // ============================================================
            foreach ($trips as $trip) {
                if (!$trip->vehicle || !$trip->vehicle->new_wheeler) {
                    continue;
                }

                $category  = $trip->vehicle->new_wheeler->name;
                $vehicleNo = $trip->vehicle->vehicle_no;

                if (!isset($categoryTotals[$category])) {
                    $categoryTotals[$category] = $blankRow();
                }

                if (!isset($report[$category][$vehicleNo])) {
                    $report[$category][$vehicleNo] = $blankRow();
                }

                // Trip count
                $report[$category][$vehicleNo]['trips']++;
                $categoryTotals[$category]['trips']++;
                $grandTotal['trips']++;

                // Journey count
                $journeyCount = $trip->tripDetails->count();
                $report[$category][$vehicleNo]['total_journeys'] += $journeyCount;
                $categoryTotals[$category]['total_journeys']     += $journeyCount;
                $grandTotal['total_journeys']                    += $journeyCount;

                // Expenses grouped by category
                $tripTotalExpense = 0;

                foreach ($trip->tripExpenses as $expense) {
                    $amount = (float) ($expense->amount ?? 0);
                    $tripTotalExpense += $amount;

                    if ($expense->expenseType && $expense->expenseType->category) {
                        $categoryName = $expense->expenseType->category->name;

                        if (isset($report[$category][$vehicleNo][$categoryName])) {
                            $report[$category][$vehicleNo][$categoryName] += $amount;
                            $categoryTotals[$category][$categoryName]     += $amount;
                            $grandTotal[$categoryName]                    += $amount;
                        }
                    }
                }

                // Total Expense
                $report[$category][$vehicleNo]['Total_Exp'] += $tripTotalExpense;
                $categoryTotals[$category]['Total_Exp']     += $tripTotalExpense;
                $grandTotal['Total_Exp']                    += $tripTotalExpense;

                // Sale / Rent from trip details
                foreach ($trip->tripDetails as $detail) {
                    $rent = (float) ($detail->rent ?? 0);
                    $report[$category][$vehicleNo]['Sale_Rent'] += $rent;
                    $categoryTotals[$category]['Sale_Rent']     += $rent;
                    $grandTotal['Sale_Rent']                    += $rent;
                }

                // Trip balance → Advance
                $tripBalance = (float) ($trip->balance ?? 0);
                $report[$category][$vehicleNo]['Advance'] += $tripBalance;
                $categoryTotals[$category]['Advance']     += $tripBalance;
                $grandTotal['Advance']                    += $tripBalance;
            }

            // ============================================================
            // DIESEL LOOP
            // ============================================================
            foreach ($diesels as $diesel) {
                if (!$diesel->vehicle || !$diesel->vehicle->new_wheeler) {
                    continue;
                }

                $category  = $diesel->vehicle->new_wheeler->name;
                $vehicleNo = $diesel->vehicle->vehicle_no;

                if (!isset($categoryTotals[$category])) {
                    $categoryTotals[$category] = $blankRow();
                }

                if (!isset($report[$category][$vehicleNo])) {
                    $report[$category][$vehicleNo] = $blankRow();
                }

                $fuelingAmount = (float) ($diesel->total_amount ?? 0);

                $fuelCategory = $expenseCategories->where('name', 'Fuel')->first();
                if ($fuelCategory) {
                    $report[$category][$vehicleNo][$fuelCategory->name] += $fuelingAmount;
                    $categoryTotals[$category][$fuelCategory->name]     += $fuelingAmount;
                    $grandTotal[$fuelCategory->name]                    += $fuelingAmount;
                }

                $report[$category][$vehicleNo]['Total_Exp'] += $fuelingAmount;
                $categoryTotals[$category]['Total_Exp']     += $fuelingAmount;
                $grandTotal['Total_Exp']                    += $fuelingAmount;
            }

            // ============================================================
            // MAINTENANCE LOOP
            // ============================================================
            foreach ($maintenances as $maintenance) {
                if (!$maintenance->vehicle || !$maintenance->vehicle->new_wheeler) {
                    continue;
                }

                $category  = $maintenance->vehicle->new_wheeler->name;
                $vehicleNo = $maintenance->vehicle->vehicle_no;

                if (!isset($categoryTotals[$category])) {
                    $categoryTotals[$category] = $blankRow();
                }

                if (!isset($report[$category][$vehicleNo])) {
                    $report[$category][$vehicleNo] = $blankRow();
                }

                $maintenanceAmount = (float) ($maintenance->amount ?? 0);

                // Maintenance column
                $report[$category][$vehicleNo]['Maintenance'] += $maintenanceAmount;
                $categoryTotals[$category]['Maintenance']     += $maintenanceAmount;
                $grandTotal['Maintenance']                    += $maintenanceAmount;

                // Total_Exp mein bhi add
                $report[$category][$vehicleNo]['Total_Exp'] += $maintenanceAmount;
                $categoryTotals[$category]['Total_Exp']     += $maintenanceAmount;
                $grandTotal['Total_Exp']                    += $maintenanceAmount;
            }

            // ============================================================
            // ISSUANCE LOOP
            // ============================================================
            foreach ($issuances as $issuance) {
                if (!$issuance->vehicle || !$issuance->vehicle->new_wheeler) {
                    continue;
                }

                $category  = $issuance->vehicle->new_wheeler->name;
                $vehicleNo = $issuance->vehicle->vehicle_no;

                if (!isset($categoryTotals[$category])) {
                    $categoryTotals[$category] = $blankRow();
                }

                if (!isset($report[$category][$vehicleNo])) {
                    $report[$category][$vehicleNo] = $blankRow();
                }

                // qty * unit_price from inventory
                $issuanceAmount = (float) ($issuance->qty ?? 0) * (float) ($issuance->inventory->unit_price ?? 0);

                $report[$category][$vehicleNo]['Inventory'] += $issuanceAmount;
                $categoryTotals[$category]['Inventory']     += $issuanceAmount;
                $grandTotal['Inventory']                    += $issuanceAmount;

                $report[$category][$vehicleNo]['Total_Exp'] += $issuanceAmount;
                $categoryTotals[$category]['Total_Exp']     += $issuanceAmount;
                $grandTotal['Total_Exp']                    += $issuanceAmount;
            }

            // ============================================================
            // TRIP PAYMENTS LOOP (Advance)
            // ============================================================
            foreach ($tripPayments as $payment) {
                if (!$payment->trip || !$payment->trip->vehicle || !$payment->trip->vehicle->new_wheeler) {
                    continue;
                }

                $category  = $payment->trip->vehicle->new_wheeler->name;
                $vehicleNo = $payment->trip->vehicle->vehicle_no;

                if (!isset($categoryTotals[$category])) {
                    $categoryTotals[$category] = $blankRow();
                }

                if (!isset($report[$category][$vehicleNo])) {
                    $report[$category][$vehicleNo] = $blankRow();
                }

                $paymentAmount = (float) ($payment->amount ?? 0);
                $report[$category][$vehicleNo]['Advance'] += $paymentAmount;
                $categoryTotals[$category]['Advance']     += $paymentAmount;
                $grandTotal['Advance']                    += $paymentAmount;
            }

            // ============================================================
            // ADVANCE EXPENSES LOOP (subtract from Advance)
            // ============================================================
            foreach ($advanceExpenses as $advExp) {
                if (!$advExp->vehicle || !$advExp->vehicle->new_wheeler) {
                    continue;
                }

                $category  = $advExp->vehicle->new_wheeler->name;
                $vehicleNo = $advExp->vehicle->vehicle_no;

                if (isset($report[$category][$vehicleNo])) {
                    $expenseAmount = (float) ($advExp->amount ?? 0);

                    // ONLY subtract from Advance, NOT from Total_Exp
                    $report[$category][$vehicleNo]['Advance'] -= $expenseAmount;
                    $categoryTotals[$category]['Advance']     -= $expenseAmount;
                    $grandTotal['Advance']                    -= $expenseAmount;
                }
            }

            // ============================================================
            // SALARY LOOP
            // ============================================================
            foreach ($drivers as $driver) {
                if (!$driver->vehicle || !$driver->vehicle->new_wheeler) {
                    continue;
                }

                $category  = $driver->vehicle->new_wheeler->name;
                $vehicleNo = $driver->vehicle->vehicle_no;

                $monthlySalary = (float) ($driver->salary ?? 0);
                $dailySalary   = $monthlySalary / 30;
                $totalSalary   = $dailySalary * $totalDays;

                if (isset($report[$category][$vehicleNo])) {
                    $salariesCategory = $expenseCategories->where('name', 'Salaries')->first();

                    if ($salariesCategory) {
                        $report[$category][$vehicleNo][$salariesCategory->name] += $totalSalary;
                        $categoryTotals[$category][$salariesCategory->name]     += $totalSalary;
                        $grandTotal[$salariesCategory->name]                    += $totalSalary;
                    }

                    $report[$category][$vehicleNo]['Salary'] += $totalSalary;
                    $categoryTotals[$category]['Salary']     += $totalSalary;
                    $grandTotal['Salary']                    += $totalSalary;

                    $report[$category][$vehicleNo]['Total_Exp'] += $totalSalary;
                    $categoryTotals[$category]['Total_Exp']     += $totalSalary;
                    $grandTotal['Total_Exp']                    += $totalSalary;
                }
            }

            // ============================================================
            // GROSS EARNING & NET EARNING
            // ============================================================
            foreach ($report as $category => $vehicles) {
                foreach ($vehicles as $vehicleNo => $data) {
                    $grossEarning = $data['Sale_Rent'];
                    $report[$category][$vehicleNo]['Gross_Earning'] = $grossEarning;
                    $categoryTotals[$category]['Gross_Earning']    += $grossEarning;
                    $grandTotal['Gross_Earning']                   += $grossEarning;

                    $netEarning = $grossEarning - $data['Total_Exp'];
                    $report[$category][$vehicleNo]['Net_Earning'] = $netEarning;
                    $categoryTotals[$category]['Net_Earning']    += $netEarning;
                    $grandTotal['Net_Earning']                   += $netEarning;
                }
            }

        return [
            'report'            => $report,
            'categoryTotals'    => $categoryTotals,
            'grandTotal'        => $grandTotal,
            'fromDate'          => $fromDate,
            'toDate'            => $toDate,
            'expenseCategories' => $expenseCategories,
        ];
    }

    public function vehicleSummaryReport(Request $request)
    {
        $data = $this->getVehicleSummaryReportData($request);
        return view('admin.reports.vehicle__summary_report', $data);
    }

    public function vehicleSummaryPdf(Request $request)
    {
        $data = $this->getVehicleSummaryReportData($request); // jo report aap already bana rahe ho
        $pdf = Pdf::loadView('admin.reports.vehicle_summary_pdf', $data)->setPaper('A4', 'landscape');
        return $pdf->download('vehicle_summary_report.pdf');
    }

    // public function trailersVehicleSummaryReport(Request $request)
    // {
    //     try {
    //         //code...
    //         // Date range set karein
    //         $fromDate = $request->filled('from_date') 
    //             ? Carbon::parse($request->from_date)->startOfDay()
    //             : Carbon::today()->startOfDay();
            
    //         $toDate = $request->filled('to_date')
    //             ? Carbon::parse($request->to_date)->endOfDay()
    //             : Carbon::today()->endOfDay();

    //         // Calculate total days in date range
    //         $totalDays = $fromDate->diffInDays($toDate) + 1;

    //         // Get all expense categories dynamically from database
    //         $expenseCategories = ExpenseCategory::where("name", '!=', 'Overheads')->orderBy('id')->get();
            
    //         // Create dynamic category array for initialization
    //         $expenseCategoryKeys = [];
    //         foreach ($expenseCategories as $category) {
    //             $expenseCategoryKeys[$category->name] = 0;
    //         }

    //         // Trips fetch karein with proper relationships - ONLY TRAILERS (vehicle_type = 2)
    //         $trips = Trip::with(['vehicle.new_wheeler', 'tripExpenses.expenseType.category', 'tripDetails'])
    //                         ->whereBetween('trip_end_date', [$fromDate, $toDate])
    //                         ->whereHas('vehicle', function($query) {
    //                             $query->where('vehicle_type', 2); // ONLY trailers
    //                         })
    //                         ->where("status", 'Ended')
    //                         ->get();

    //         // Diesel with trip relationship - ONLY TRAILERS
    //         $diesels = Diesel::with(['vehicle.new_wheeler', 'trip'])
    //                     ->whereBetween('date', [$fromDate, $toDate])
    //                     ->whereHas('vehicle', function($query) {
    //                         $query->where('vehicle_type', 2); // ONLY trailers
    //                     })
    //                     ->get();


    //         // ---- Maintenance fetch ----
    //         $maintenances = Maintenance::with(['vehicle.new_wheeler'])
    //                                     ->whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()])
    //                                     ->whereHas('vehicle', function ($query) {
    //                                         $query->where('vehicle_type', 2);
    //                                     })
    //                                     ->get();

                                        
    //         // ---- Issuances fetch ----
    //         $issuances = Issuance::with(['vehicle.new_wheeler', 'inventory'])
    //                                 ->whereBetween('issue_date', [$fromDate->toDateString(), $toDate->toDateString()])
    //                                 ->whereHas('vehicle', function ($query) {
    //                                     $query->where('vehicle_type', 2);
    //                                 })
    //                                 ->get();

    //         // $inventories = Inventory::whereBetween('purchase_date', [$fromDate->toDateString(), $toDate->toDateString()])
    //         //                         ->sum("price");

    //         // $overheads = Overhead::whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()])->sum("amount");

                                    

    //         // Drivers fetch karein with vehicle relationship - ONLY TRAILERS
    //         $drivers = Driver::with('vehicle')
    //                     ->where('status', 'active')
    //                     ->whereHas('vehicle', function($query) {
    //                         $query->where('vehicle_type', 2); // ONLY trailers
    //                     })
    //                     ->get();

    //         // Fetch TripPayments (Advance payments) for date range - ONLY TRAILERS
    //         $tripPayments = TripPayment::with(['trip.vehicle.new_wheeler'])
    //                         ->whereBetween('date', [$fromDate, $toDate])
    //                         ->whereHas('trip.vehicle', function($query) {
    //                             $query->where('vehicle_type', 2); // ONLY trailers
    //                         })
    //                         ->get();

    //         // Fetch TripVehicleExpense where expense_from = "From Advance Amount" - ONLY TRAILERS
    //         $advanceExpenses = TripVehicleExpense::with(['vehicle.new_wheeler', 'trip', 'expenseType.category'])
    //                             ->where('expense_from', 'From Advance Amount')
    //                             ->whereHas('vehicle', function($query) {
    //                                 $query->where('vehicle_type', 2); // ONLY trailers
    //                             })
    //                             ->whereHas('trip', function($query) use ($fromDate, $toDate) {
    //                                 $query->whereBetween('trip_end_date', [$fromDate, $toDate]);
    //                             })
    //                             ->get();

    //         $report = [];

    //         // ---- Helper closure to build a fresh row ----
    //         $blankRow = function () use ($expenseCategoryKeys) {
    //             return array_merge([
    //                 'trips'          => 0,
    //                 'total_journeys' => 0,
    //             ], $expenseCategoryKeys, [
    //                 'Advance'       => 0,
    //                 'Salary'        => 0,
    //                 'Maintenance'   => 0,
    //                 'Inventory'     => 0,
    //                 'Total_Exp'     => 0,
    //                 'Sale_Rent'     => 0,
    //                 'Gross_Earning' => 0,
    //                 'Net_Earning'   => 0,
    //             ]);
    //         };
            
    //         // Grand total initialize - with dynamic expense categories
    //         $grandTotal = array_merge([
    //             'trips'         => 0,
    //             'total_journeys' => 0,
    //         ], $expenseCategoryKeys, [
    //             'Advance'       => 0,
    //             'Salary'        => 0,
    //             'Maintenance'   => 0,
    //             'Inventory'     => 0, 
    //             'Total_Exp'     => 0,
    //             'Sale_Rent'     => 0,
    //             'Gross_Earning' => 0,
    //             'Net_Earning'   => 0
    //         ]);

    //         // Category-wise totals initialize
    //         $categoryTotals = [];

    //         foreach ($trips as $trip) {
    //             // Safety checks
    //             if (!$trip->vehicle || !$trip->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category  = $trip->vehicle->new_wheeler->name;
    //             $vehicleNo = $trip->vehicle->vehicle_no;

    //             // Initialize category total agar pehle se nahi hai
    //             if (!isset($categoryTotals[$category])) {
    //                 $categoryTotals[$category] = array_merge([
    //                     'trips'         => 0,
    //                     'total_journeys' => 0,
    //                 ], $expenseCategoryKeys, [
    //                     'Advance'       => 0,
    //                     'Salary'        => 0,
    //                     'Maintenance'   => 0,
    //                     'Inventory'     => 0,
    //                     'Total_Exp'     => 0,
    //                     'Sale_Rent'     => 0,
    //                     'Gross_Earning' => 0,
    //                     'Net_Earning'   => 0
    //                 ]);
    //             }

    //             // Initialize vehicle row agar pehle se nahi hai
    //             if (!isset($report[$category][$vehicleNo])) {
    //                 $report[$category][$vehicleNo] = array_merge([
    //                     'trips'         => 0,
    //                     'total_journeys' => 0,
    //                 ], $expenseCategoryKeys, [
    //                     'Advance'       => 0,
    //                     'Salary'        => 0,
    //                     'Maintenance'   => 0,
    //                     'Inventory'     => 0,

    //                     'Total_Exp'     => 0,
    //                     'Sale_Rent'     => 0,
    //                     'Gross_Earning' => 0,
    //                     'Net_Earning'   => 0
    //                 ]);
    //             }

    //             // Trip count
    //             $report[$category][$vehicleNo]['trips']++;
    //             $categoryTotals[$category]['trips']++;
    //             $grandTotal['trips']++;

    //             // Journey count (tripDetails ka count)
    //             $journeyCount = $trip->tripDetails->count();
    //             $report[$category][$vehicleNo]['total_journeys'] += $journeyCount;
    //             $categoryTotals[$category]['total_journeys']     += $journeyCount;
    //             $grandTotal['total_journeys']                    += $journeyCount;

    //             // Expenses calculate karein - GROUP BY CATEGORY
    //             $tripTotalExpense = 0;
                
    //             foreach ($trip->tripExpenses as $expense) {
    //                 $amount = (float) ($expense->amount ?? 0);
    //                 $tripTotalExpense += $amount;
                    
    //                 // Get category name from expense type relationship
    //                 if ($expense->expenseType && $expense->expenseType->category) {
    //                     $categoryName = $expense->expenseType->category->name;
                        
    //                     // Add to respective CATEGORY column (not expense type)
    //                     if (isset($report[$category][$vehicleNo][$categoryName])) {
    //                         $report[$category][$vehicleNo][$categoryName] += $amount;
    //                         $categoryTotals[$category][$categoryName] += $amount;
    //                         $grandTotal[$categoryName] += $amount;
    //                     }
    //                 }
    //             }

    //             // Total Expense add karo
    //             $report[$category][$vehicleNo]['Total_Exp'] += $tripTotalExpense;
    //             $categoryTotals[$category]['Total_Exp'] += $tripTotalExpense;
    //             $grandTotal['Total_Exp'] += $tripTotalExpense;

    //             // Trip details se rent calculate karein
    //             foreach ($trip->tripDetails as $detail) {
    //                 $rent = (float) ($detail->rent ?? 0);
    //                 $report[$category][$vehicleNo]['Sale_Rent'] += $rent;
    //                 $categoryTotals[$category]['Sale_Rent'] += $rent;
    //                 $grandTotal['Sale_Rent'] += $rent;
    //             }

    //             // Trip balance ko Advance mein add/subtract karein
    //             // Positive balance = add to Advance
    //             // Negative balance = subtract from Advance
    //             $tripBalance = (float) ($trip->balance ?? 0);
                
    //             $report[$category][$vehicleNo]['Advance'] += $tripBalance;
    //             $categoryTotals[$category]['Advance']     += $tripBalance;
    //             $grandTotal['Advance']                    += $tripBalance;
    //         }

    //         // Diesel entries process karein
    //         foreach ($diesels as $diesel) {
    //             // Safety checks
    //             if (!$diesel->vehicle || !$diesel->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category  = $diesel->vehicle->new_wheeler->name;
    //             $vehicleNo = $diesel->vehicle->vehicle_no;


    //             if (!isset($categoryTotals[$category])) {
    //                 $categoryTotals[$category] = $blankRow();
    //             }

    //             if (!isset($report[$category][$vehicleNo])) {
    //                 $report[$category][$vehicleNo] = $blankRow();
    //             }

    //             // Fueling amount add karo
    //             $fuelingAmount = (float) ($diesel->total_amount ?? 0);
                
    //             // Find "Fuel" category dynamically from database
    //             $fuelCategory = $expenseCategories->where('name', 'Fuel')->first();
    //             if ($fuelCategory) {
    //                 // Add diesel amount to Fuel category
    //                 $report[$category][$vehicleNo][$fuelCategory->name] += $fuelingAmount;
    //                 $categoryTotals[$category][$fuelCategory->name] += $fuelingAmount;
    //                 $grandTotal[$fuelCategory->name] += $fuelingAmount;
    //             }

    //             // Diesel amount ko Total_Exp mein bhi add karo
    //             $report[$category][$vehicleNo]['Total_Exp'] += $fuelingAmount;
    //             $categoryTotals[$category]['Total_Exp'] += $fuelingAmount;
    //             $grandTotal['Total_Exp'] += $fuelingAmount;
    //         }

    //         foreach ($maintenances as $maintenance) {
    //             if (!$maintenance->vehicle || !$maintenance->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category  = $maintenance->vehicle->new_wheeler->name;
    //             $vehicleNo = $maintenance->vehicle->vehicle_no;

    //             if (!isset($categoryTotals[$category])) {
    //                 $categoryTotals[$category] = $blankRow();
    //             }

    //             if (!isset($report[$category][$vehicleNo])) {
    //                 $report[$category][$vehicleNo] = $blankRow();
    //             }

    //             $maintenanceAmount = (float) ($maintenance->amount ?? 0);

    //             // Maintenance column
    //             $report[$category][$vehicleNo]['Maintenance'] += $maintenanceAmount;
    //             $categoryTotals[$category]['Maintenance']     += $maintenanceAmount;
    //             $grandTotal['Maintenance']                    += $maintenanceAmount;

    //             // Total_Exp mein bhi add
    //             $report[$category][$vehicleNo]['Total_Exp'] += $maintenanceAmount;
    //             $categoryTotals[$category]['Total_Exp']     += $maintenanceAmount;
    //             $grandTotal['Total_Exp']                    += $maintenanceAmount;
    //         }

    //         // ============================================================
    //         // ISSUANCE LOOP
    //         // ============================================================
    //         foreach ($issuances as $issuance) {
    //             if (!$issuance->vehicle || !$issuance->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category  = $issuance->vehicle->new_wheeler->name;
    //             $vehicleNo = $issuance->vehicle->vehicle_no;

    //             if (!isset($categoryTotals[$category])) {
    //                 $categoryTotals[$category] = $blankRow();
    //             }

    //             if (!isset($report[$category][$vehicleNo])) {
    //                 $report[$category][$vehicleNo] = $blankRow();
    //             }

    //             // qty * unit_price from inventory
    //             $issuanceAmount = (float) ($issuance->qty ?? 0) * (float) ($issuance->inventory->unit_price ?? 0);

    //             $report[$category][$vehicleNo]['Inventory'] += $issuanceAmount;
    //             $categoryTotals[$category]['Inventory']     += $issuanceAmount;
    //             $grandTotal['Inventory']                    += $issuanceAmount;

    //             $report[$category][$vehicleNo]['Total_Exp'] += $issuanceAmount;
    //             $categoryTotals[$category]['Total_Exp']     += $issuanceAmount;
    //             $grandTotal['Total_Exp']                    += $issuanceAmount;
    //         }

            

    //         // Calculate Advance: TripPayments - Expenses from Advance
    //         foreach ($tripPayments as $payment) {
    //             if (!$payment->trip || !$payment->trip->vehicle || !$payment->trip->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category = $payment->trip->vehicle->new_wheeler->name;
    //             $vehicleNo = $payment->trip->vehicle->vehicle_no;

    //             if (!isset($categoryTotals[$category])) {
    //                 $categoryTotals[$category] = $blankRow();
    //             }

    //             if (!isset($report[$category][$vehicleNo])) {
    //                 $report[$category][$vehicleNo] = $blankRow();
    //             }

    //             $paymentAmount = (float) ($payment->amount ?? 0);
    //             $report[$category][$vehicleNo]['Advance'] += $paymentAmount;
    //             $categoryTotals[$category]['Advance'] += $paymentAmount;
    //             $grandTotal['Advance'] += $paymentAmount;
    //         }

    //         // Subtract expenses paid from advance
    //         foreach ($advanceExpenses as $advExp) {
    //             if (!$advExp->vehicle || !$advExp->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category = $advExp->vehicle->new_wheeler->name;
    //             $vehicleNo = $advExp->vehicle->vehicle_no;

    //             if (isset($report[$category][$vehicleNo])) {
    //                 $expenseAmount = (float) ($advExp->amount ?? 0);
                    
    //                 // ONLY subtract from Advance, DO NOT add to Total_Exp
    //                 $report[$category][$vehicleNo]['Advance'] -= $expenseAmount;
    //                 $categoryTotals[$category]['Advance'] -= $expenseAmount;
    //                 $grandTotal['Advance'] -= $expenseAmount;
    //             }
    //         }

    //         // Calculate salary for each vehicle
    //         foreach ($drivers as $driver) {
    //             if (!$driver->vehicle || !$driver->vehicle->new_wheeler) {
    //                 continue;
    //             }

    //             $category = $driver->vehicle->new_wheeler->name;
    //             $vehicleNo = $driver->vehicle->vehicle_no;

    //             $monthlySalary = (float) ($driver->salary ?? 0);
    //             $dailySalary = $monthlySalary / 30;
    //             $totalSalary = $dailySalary * $totalDays;

    //             if (isset($report[$category][$vehicleNo])) {
    //                 // Find "Salaries" category from database
    //                 $salariesCategory = $expenseCategories->where('name', 'Salaries')->first();
                    
    //                 if ($salariesCategory) {
    //                     // Add to Salaries CATEGORY column
    //                     $report[$category][$vehicleNo][$salariesCategory->name] += $totalSalary;
    //                     $categoryTotals[$category][$salariesCategory->name] += $totalSalary;
    //                     $grandTotal[$salariesCategory->name] += $totalSalary;
    //                 }
                    
    //                 // Also keep in Salary for second table
    //                 $report[$category][$vehicleNo]['Salary'] += $totalSalary;
    //                 $categoryTotals[$category]['Salary']     += $totalSalary;
    //                 $grandTotal['Salary'] += $totalSalary;

    //                 // Salary ko Total_Exp mein bhi add karo
    //                 $report[$category][$vehicleNo]['Total_Exp'] += $totalSalary;
    //                 $categoryTotals[$category]['Total_Exp']     += $totalSalary;
    //                 $grandTotal['Total_Exp'] += $totalSalary;
    //             }
    //         }

    //         // Calculate Gross Earning and Net Earning
    //         foreach ($report as $category => $vehicles) {
    //             foreach ($vehicles as $vehicleNo => $data) {
    //                 $grossEarning                                   = $data['Sale_Rent'];
    //                 $report[$category][$vehicleNo]['Gross_Earning'] = $grossEarning;
    //                 $categoryTotals[$category]['Gross_Earning']     += $grossEarning;
    //                 $grandTotal['Gross_Earning']                    += $grossEarning;

    //                 $netEarning                                     = $grossEarning - $data['Total_Exp'];
    //                 $report[$category][$vehicleNo]['Net_Earning']   = $netEarning;
    //                 $categoryTotals[$category]['Net_Earning']       += $netEarning;
    //                 $grandTotal['Net_Earning'] += $netEarning;
    //             }
    //         }

    //         return view('admin.reports.trailers_vehicle_summary_report', compact(
    //             'report', 
    //             'categoryTotals', 
    //             'grandTotal', 
    //             'fromDate', 
    //             'toDate',
    //             'expenseCategories',
    //             // 'inventories',
    //             // 'overheads'
    //         ));
    //     } catch (\Exception $e) {
    //         return redirect()->back()->with('error', $e->getMessage());

    //     }
    // }

    private function getTrailerSummaryReportData(Request $request)
    {
        $fromDate = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : Carbon::today()->startOfDay();

        $toDate = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::today()->endOfDay();

        $totalDays = $fromDate->diffInDays($toDate) + 1;

        $expenseCategories = ExpenseCategory::where('name','!=','Overheads')->get();

        $expenseKeys = [];
        foreach ($expenseCategories as $cat) {
            $expenseKeys[$cat->name] = 0;
        }

        $blankRow = function () use ($expenseKeys) {
            return array_merge([
                'trips'=>0,
                'total_journeys'=>0,
            ],$expenseKeys,[
                'Advance'=>0,
                'Salary'=>0,
                'Maintenance'=>0,
                'Inventory'=>0,
                'Total_Exp'=>0,
                'Sale_Rent'=>0,
                'Gross_Earning'=>0,
                'Net_Earning'=>0
            ]);
        };

        $report = [];
        $categoryTotals = [];
        $grandTotal = $blankRow();

        /*
        |--------------------------------------------------------------------------
        | TRIPS
        |--------------------------------------------------------------------------
        */

        $trips = Trip::with([
            'vehicle.new_wheeler',
            'tripExpenses.expenseType.category',
            'tripDetails'
        ])
        ->whereBetween('trip_end_date',[$fromDate,$toDate])
        ->whereHas('vehicle',fn($q)=>$q->where('vehicle_type',2))
        ->where('status','Ended')
        ->get();

        foreach($trips as $trip){

            if(!$trip->vehicle || !$trip->vehicle->new_wheeler){
                continue;
            }

            $category = $trip->vehicle->new_wheeler->name;
            $vehicleNo = $trip->vehicle->vehicle_no;

            if(!isset($report[$category][$vehicleNo])){
                $report[$category][$vehicleNo] = $blankRow();
            }

            if(!isset($categoryTotals[$category])){
                $categoryTotals[$category] = $blankRow();
            }

            /* Trips */
            $report[$category][$vehicleNo]['trips']++;
            $categoryTotals[$category]['trips']++;
            $grandTotal['trips']++;

            /* Journeys */
            $journeys = $trip->tripDetails->count();

            $report[$category][$vehicleNo]['total_journeys'] += $journeys;
            $categoryTotals[$category]['total_journeys'] += $journeys;
            $grandTotal['total_journeys'] += $journeys;

            /* Rent */
            foreach($trip->tripDetails as $detail){

                $rent = (float)$detail->rent;

                $report[$category][$vehicleNo]['Sale_Rent'] += $rent;
                $categoryTotals[$category]['Sale_Rent'] += $rent;
                $grandTotal['Sale_Rent'] += $rent;
            }

            /* Trip Expenses */
            foreach($trip->tripExpenses as $expense){

                $amount = (float)$expense->amount;

                if($expense->expenseType && $expense->expenseType->category){

                    $cat = $expense->expenseType->category->name;

                    if(isset($report[$category][$vehicleNo][$cat])){

                        $report[$category][$vehicleNo][$cat] += $amount;
                        $categoryTotals[$category][$cat] += $amount;
                        $grandTotal[$cat] += $amount;

                        $report[$category][$vehicleNo]['Total_Exp'] += $amount;
                        $categoryTotals[$category]['Total_Exp'] += $amount;
                        $grandTotal['Total_Exp'] += $amount;
                    }
                }
            }

            /* Advance (Trip Balance Only) */

            $balance = (float)$trip->balance;

            $report[$category][$vehicleNo]['Advance'] += $balance;
            $categoryTotals[$category]['Advance'] += $balance;
            $grandTotal['Advance'] += $balance;
        }

        /*
        |--------------------------------------------------------------------------
        | DIESEL
        |--------------------------------------------------------------------------
        */

        $diesels = Diesel::with('vehicle.new_wheeler')
        ->whereBetween('date',[$fromDate,$toDate])
        ->whereHas('vehicle',fn($q)=>$q->where('vehicle_type',2))
        ->get();

        foreach($diesels as $diesel){

            if(!$diesel->vehicle || !$diesel->vehicle->new_wheeler){
                continue;
            }

            $category = $diesel->vehicle->new_wheeler->name;
            $vehicleNo = $diesel->vehicle->vehicle_no;

            if(!isset($report[$category][$vehicleNo])){
                $report[$category][$vehicleNo] = $blankRow();
            }

            if(!isset($categoryTotals[$category])){
                $categoryTotals[$category] = $blankRow();
            }

            $amount = (float)$diesel->total_amount;

            $report[$category][$vehicleNo]['Fuel'] += $amount;
            $categoryTotals[$category]['Fuel'] += $amount;
            $grandTotal['Fuel'] += $amount;

            $report[$category][$vehicleNo]['Total_Exp'] += $amount;
            $categoryTotals[$category]['Total_Exp'] += $amount;
            $grandTotal['Total_Exp'] += $amount;
        }

        /*
        |--------------------------------------------------------------------------
        | MAINTENANCE
        |--------------------------------------------------------------------------
        */

        $maintenances = Maintenance::with('vehicle.new_wheeler')
        ->whereBetween('date',[$fromDate,$toDate])
            ->whereHas('vehicle',fn($q)=>$q->where('vehicle_type',2))
            ->get();

        foreach($maintenances as $maintenance){

            if(!$maintenance->vehicle || !$maintenance->vehicle->new_wheeler){
                continue;
            }

            $category = $maintenance->vehicle->new_wheeler->name;
            $vehicleNo = $maintenance->vehicle->vehicle_no;

            if(!isset($report[$category][$vehicleNo])){
                $report[$category][$vehicleNo] = $blankRow();
            }

            if(!isset($categoryTotals[$category])){
                $categoryTotals[$category] = $blankRow();
            }

            $amount = (float)$maintenance->amount;

            $report[$category][$vehicleNo]['Maintenance'] += $amount;
            $categoryTotals[$category]['Maintenance'] += $amount;
            $grandTotal['Maintenance'] += $amount;

            $report[$category][$vehicleNo]['Total_Exp'] += $amount;
            $categoryTotals[$category]['Total_Exp'] += $amount;
            $grandTotal['Total_Exp'] += $amount;
        }

        /*
        |--------------------------------------------------------------------------
        | SALARY
        |--------------------------------------------------------------------------
        */

        $drivers = Driver::with('vehicle.new_wheeler')
            ->where('status','active')
            ->whereHas('vehicle',fn($q)=>$q->where('vehicle_type',2))
            ->get();

        foreach($drivers as $driver){

            if(!$driver->vehicle || !$driver->vehicle->new_wheeler){
                continue;
            }

            $category = $driver->vehicle->new_wheeler->name;
            $vehicleNo = $driver->vehicle->vehicle_no;

            $salary = ($driver->salary / 30) * $totalDays;

            if(isset($report[$category][$vehicleNo])){

                $report[$category][$vehicleNo]['Salary'] += $salary;
                $categoryTotals[$category]['Salary'] += $salary;
                $grandTotal['Salary'] += $salary;

                $report[$category][$vehicleNo]['Total_Exp'] += $salary;
                $categoryTotals[$category]['Total_Exp'] += $salary;
                $grandTotal['Total_Exp'] += $salary;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | EARNINGS
        |--------------------------------------------------------------------------
        */

        foreach($report as $cat => $vehicles){

            foreach($vehicles as $veh => $data){

                $gross = $data['Sale_Rent'];
                $net = $gross - $data['Total_Exp'];

                $report[$cat][$veh]['Gross_Earning'] = $gross;
                $report[$cat][$veh]['Net_Earning'] = $net;

                $categoryTotals[$cat]['Gross_Earning'] += $gross;
                $categoryTotals[$cat]['Net_Earning'] += $net;

                $grandTotal['Gross_Earning'] += $gross;
                $grandTotal['Net_Earning'] += $net;
            }
        }

        return [
            'report'=>$report,
            'categoryTotals'=>$categoryTotals,
            'grandTotal'=>$grandTotal,
            'fromDate'=>$fromDate,
            'toDate'=>$toDate,
            'expenseCategories'=>$expenseCategories
        ];
    }

    public function trailersVehicleSummaryReport(Request $request)
    {
        $data = $this->getTrailerSummaryReportData($request);
        return view('admin.reports.trailers_vehicle_summary_report', $data);
    }

    public function trailerVehicleSummaryPdf(Request $request)
    {
        $data = $this->getTrailerSummaryReportData($request);
        $pdf  = Pdf::loadView('admin.reports.trailer_vehicle_summary_pdf', $data)
                ->setPaper('A4', 'landscape');

        return $pdf->download('trailer_vehicle_summary_report.pdf');
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

    // ============================================================
    // Add these methods to your existing ReportController
    // (or whichever controller handles reports)
    // ============================================================

    // Required imports at the top of your controller:
    // use App\Models\Inventory;
    // use App\Models\InventoryItem;
    // use App\Models\Issuance;
    // use Carbon\Carbon;
    // use Barryvdh\DomPDF\Facade\Pdf;

    // ============================================================
    // ROUTES to add in web.php:
    //
    // Route::get('admin/reports/inventory',      [ReportController::class, 'inventoryReport'])
    //      ->name('admin.inventory.report');
    //
    // Route::get('admin/reports/inventory/pdf',  [ReportController::class, 'inventoryReportPdf'])
    //      ->name('admin.inventory.report.pdf');
    // ============================================================


    private function getInventoryReportData(Request $request)
    {
        // ---- Date Range ----
        $fromDate = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : Carbon::today()->startOfMonth()->startOfDay();

        $toDate = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::today()->endOfDay();

        // ---- Build Inventory query ----
        $inventoryQuery = Inventory::with('item')
            ->whereBetween('purchase_date', [$fromDate->toDateString(), $toDate->toDateString()]);

        $inventories = $inventoryQuery->orderBy('purchase_date', 'desc')->get();

        // ---- All Items (for filter dropdown) ----
        $items = InventoryItem::orderBy('name')->get();

        // ---- Stock Summary per Item ----
        // Total purchased, total issued, remaining stock, total value
        $stockSummaryQuery = InventoryItem::withSum(
                                        ['inventories as total_purchased' => function ($q) use ($fromDate, $toDate) {
                                            $q->whereBetween('purchase_date', [$fromDate->toDateString(), $toDate->toDateString()]);
                                        }], 'qty'
                                    )
                                    ->withSum(
                                        ['inventories as total_remaining' => function ($q) use ($fromDate, $toDate) {
                                            $q->whereBetween('purchase_date', [$fromDate->toDateString(), $toDate->toDateString()]);
                                        }], 'remaining_qty'
                                    )
                                    ->withSum(
                                        ['inventories as total_value' => function ($q) use ($fromDate, $toDate) {
                                            $q->whereBetween('purchase_date', [$fromDate->toDateString(), $toDate->toDateString()]);
                                        }], 'total_price'
                                    )
                                    ->having('total_purchased', '>', 0);
        $stockSummaryRaw = $stockSummaryQuery->get();

        // ---- Total Issued (from Issuances table) ----
        $issuanceTotals = Issuance::whereBetween('issue_date', [$fromDate->toDateString(), $toDate->toDateString()])
                                    ->selectRaw('item_id, SUM(qty) as total_issued')
                                    ->groupBy('item_id')
                                    ->pluck('total_issued', 'item_id');

        // Merge issued qty into stock summary
        $stockSummary = $stockSummaryRaw->map(function ($item) use ($issuanceTotals) {
                            $item->total_issued      = $issuanceTotals[$item->id] ?? 0;
                            $item->remaining_stock   = ($item->total_remaining ?? 0);
                            $item->total_purchased   = $item->total_purchased ?? 0;
                            $item->total_value       = $item->total_value ?? 0;
                            return $item;
                        });

        // ---- Summary totals ----
        $summary = [
            'total_items'          => $stockSummary->count(),
            'total_purchased_qty'  => $inventories->sum('qty'),
            'total_issued_qty'     => $issuanceTotals->sum(),
            'total_amount'         => $inventories->sum('total_price'),
        ];

        return [
            'inventories'  => $inventories,
            'items'        => $items,
            'stockSummary' => $stockSummary,
            'summary'      => $summary,
            'fromDate'     => $fromDate,
            'toDate'       => $toDate,
        ];
    }

    public function inventoryReport(Request $request)
    {
        $data = $this->getInventoryReportData($request);
        return view('admin.reports.inventory_report', $data);
    }

    public function inventoryReportPdf(Request $request)
    {
        $data = $this->getInventoryReportData($request);
        $pdf  = Pdf::loadView('admin.reports.inventory_report_pdf', $data)->setPaper('A4', 'landscape');
        return $pdf->download('inventory_report_' . now()->format('Ymd') . '.pdf');
    }

    // ============================================================
    // Add these methods to your ReportController
    // ============================================================

    // Required imports:
    // use App\Models\Overhead;
    // use App\Models\ExpenseType;
    // use App\Models\Driver;
    // use Carbon\Carbon;
    // use Barryvdh\DomPDF\Facade\Pdf;
    // use Illuminate\Support\Facades\DB;

    // ============================================================
    // ROUTES to add in web.php:
    //
    // Route::get('admin/reports/overhead',     [ReportController::class, 'overheadReport'])
    //      ->name('admin.overhead.report');
    //
    // Route::get('admin/reports/overhead/pdf', [ReportController::class, 'overheadReportPdf'])
    //      ->name('admin.overhead.report.pdf');
    // ============================================================


    private function getOverheadReportData(Request $request)
    {
        // ---- Date Range ----
        $fromDate = $request->filled('from_date')
            ? Carbon::parse($request->from_date)->startOfDay()
            : Carbon::today()->startOfMonth()->startOfDay();

        $toDate = $request->filled('to_date')
            ? Carbon::parse($request->to_date)->endOfDay()
            : Carbon::today()->endOfDay();

        $totalDays = max(1, $fromDate->diffInDays($toDate) + 1);

        // ---- Overheads query ----
        $query = Overhead::with(['expenseType.category', 'driver'])
            ->whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()]);

        if ($request->filled('expense_type_id')) {
            $query->where('expense_type_id', $request->expense_type_id);
        }

        $overheads = $query->orderBy('date', 'desc')->get();

        // ---- Category totals ----
        $categoryTotals = Overhead::query()
                                ->join('expense_types', 'overheads.expense_type_id', '=', 'expense_types.id')
                                ->join('expense_categories', 'expense_types.category_id', '=', 'expense_categories.id')
                                ->whereBetween('overheads.date', [$fromDate->toDateString(), $toDate->toDateString()])
                                ->when($request->filled('expense_type_id'), fn($q) => $q->where('overheads.expense_type_id', $request->expense_type_id))
                                ->selectRaw('expense_categories.name as category_name, COUNT(*) as record_count, SUM(overheads.amount) as total_amount')
                                ->groupBy('expense_categories.id', 'expense_categories.name')
                                ->orderByDesc('total_amount')
                                ->get();

        // ---- Filter dropdowns ----
        $expenseTypes = ExpenseType::with('category')->orderBy('name')->get();
        $drivers      = Driver::orderBy('name')->get();

        // ---- Summary ----
        $summary = [
            'total_records' => $overheads->count(),
            'total_amount'  => $overheads->sum('amount'),
            'unique_types'  => $overheads->pluck('expense_type_id')->unique()->count(),
            'avg_per_day'   => $overheads->sum('amount') / $totalDays,
        ];

        return [
            'overheads'      => $overheads,
            'categoryTotals' => $categoryTotals,
            'expenseTypes'   => $expenseTypes,
            'drivers'        => $drivers,
            'summary'        => $summary,
            'fromDate'       => $fromDate,
            'toDate'         => $toDate,
        ];
    }


    public function overheadReport(Request $request)
    {
        $data = $this->getOverheadReportData($request);
        return view('admin.reports.overhead_report', $data);
    }


    public function overheadReportPdf(Request $request)
    {
        $data = $this->getOverheadReportData($request);
        $pdf  = Pdf::loadView('admin.reports.overhead_report_pdf', $data)
                ->setPaper('A4', 'landscape');
        return $pdf->download('overhead_report_' . now()->format('Ymd') . '.pdf');
    }
}

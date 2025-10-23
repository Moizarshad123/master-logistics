<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripDetail;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\TripVehicleExpense;
use App\Models\TripPayment;
use App\Models\ExpenseType;
use App\Models\Destination;
use Illuminate\Http\Request;
use App\Models\PurchaseSheet;
use App\Models\SaleSheet;
use App\Models\Material;
use App\Models\ExpenseFrom;



use DB, DataTables;

class TripController extends Controller
{
    public function index()
    {

        try {
            if (request()->ajax()) {
            
                $trips = Trip::with('tripDetails', 'vehicle', 'driver')->latest()->get();

                return datatables()->of($trips)
                    ->addColumn('vehicle', function ($data) {
                        if($data->vehicle != null) {
                            return $data->vehicle->vehicle_no;
                        } else {
                            return "";
                        }
                    })
                    ->editColumn('trip_date', function ($data) {
                       
                        return date("d-m-Y", strtotime($data->trip_date));
                        
                    })

                    
                    ->addColumn('driver', function ($data) {
                        if($data->driver != null) {
                            return $data->driver->name;
                        } else {
                            return "";
                        }
                    })
                    ->addColumn('journey_count', function ($data) {
                        return  $data->tripDetails->count() ?? 0;
                    })
                    ->editColumn('created_at', function ($data) {
                        return  date('d M Y', strtotime($data->created_at));
                    })   
                    ->addColumn('action', function ($data) {

                        $viewUrl   = route('admin.trips.show', $data->id);
                        $editUrl   = route('admin.trips.edit', $data->id);
                        $deleteUrl = route('admin.trips.destroy', $data->id);

                        return '
                            <a href="'.$viewUrl.'" class="btn btn-sm btn-info">View</a> |
                            <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> |
                            <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>
                        ';
                    })
                    ->rawColumns(['action', 'vehicle', 'driver', 'journey_count'])->make(true);

            }

        } catch (\Exception $ex) {
            return redirect('/')->with('error', $ex->getMessage());
        }

        return view('admin.trips.index');
    }

    public function create()
    {
        $vehicles     = Vehicle::all();
        $drivers      = Driver::all();
        $expenses     = ExpenseType::all();
        $destinations = Destination::all();
        $sales        = SaleSheet::orderByDESC("id")->get();
        $purchases    = PurchaseSheet::orderByDESC("id")->get();
        $materials    = Material::orderBy("name", "ASC")->get();
        $expense_froms = ExpenseFrom::orderBy("name", "ASC")->get();

        
        return view('admin.trips.create', compact("expense_froms", "materials", 'vehicles', 'drivers', "expenses", "destinations", "sales", "purchases"));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'vehicle_id' => 'required',
                'driver_id'  => 'required',
            ]);

    
            DB::beginTransaction();
    
            $trip_no = str_pad(Trip::max('id') + 1, 2, '0', STR_PAD_LEFT);
            
            $trip = Trip::create([
                                'trip_no'    => $trip_no,
                                // "trip_type"  => $request->trip_type,
                                'vehicle_id' => $request->vehicle_id,
                                'driver_id'  => $request->driver_id,
                                "balance"    => $request->balance,
                                "trip_date"  => $request->trip_date 
                            ]);


            if (!empty($request->payment_type) && is_array($request->payment_type)) {
                foreach ($request->payment_type as $index => $type) {
                    TripPayment::create([
                        'trip_id'      => $trip->id,
                        'driver_id'    => $request->driver_id,
                        'payment_type' => $type,
                        'amount'       => $request->expense_amount[$index] ?? 0,
                        'date'         => $request->date[$index] ?? now(),
                        'comments'     => $request->comments[$index] ?? null,
                    ]);
                }
            }

            if (!empty($request->expenses)) {
                foreach ($request->expenses as $expenseData) {

                    if (empty($expenseData['name']) || empty($expenseData['amount'])) {
                        continue;
                    }
                    TripVehicleExpense::create([
                        'trip_id'    => $trip->id,
                        'vehicle_id' => $request->vehicle_id,
                        'expense'    => $expenseData['name'],
                        'expense_from' => $expenseData["expense_from"],
                        'amount'     => $expenseData['amount'],
                    ]);
                }
            }
    
            // Save trip details
            if ($request->trip_details) {
                foreach ($request->trip_details as $detail) {
                    $trip->tripDetails()->create($detail);
                }
            }
            DB::commit();
            return redirect()->route('admin.trips.index')->with('success', 'Trip created successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());

        }
    }

    public function show(Trip $trip)
    {
        $trip->load(['tripDetails.from_dest', 'tripDetails.to_dest', 'vehicle', 'driver', 'tripDetails', 'tripPayments', 'tripExpenses', 'tripExpenses.expenseName']);
        return view('admin.trips.detail', compact('trip'));
    }

    public function edit(Trip $trip)
    {
        $trip->load(['tripDetails' => function($query) {
            $query->whereNull('end_date');
        }]);
        $vehicles      = Vehicle::all();
        $drivers       = Driver::all();
        $expensesTypes = ExpenseType::all();
        $expenses     = TripVehicleExpense::with("expenseName")->where("trip_id", $trip->id)->get();
        $payments     = TripPayment::where("trip_id", $trip->id)->get();
        $destinations = Destination::all();
        $sales        = SaleSheet::orderByDESC("id")->get();
        $purchases    = PurchaseSheet::orderByDESC("id")->get();
        $materials    = Material::orderBy("name", "ASC")->get();

        return view('admin.trips.edit', compact("sales", "purchases", "materials", 'trip', 'vehicles', 'drivers', 'expenses', 'payments', 'expensesTypes', 'destinations'));
    }

    public function update(Request $request, Trip $trip)
    {
        try {
            $request->validate([
               'trip_type'  => 'required',
               'vehicle_id' => 'required',
               'driver_id'  => 'required',
            ]);
   
           DB::beginTransaction();

           $trip->update($request->only('trip_no', 'trip_date', 'trip_type', 'vehicle_id', 'driver_id'));

            $paymentTypes   = $request->payment_type;
            $amounts        = $request->expense_amount;
            $dates          = $request->date;
            $comments       = $request->comments;
            $paymentIds     = $request->payment_id ?? []; // may not exist for new rows

            for ($i = 0; $i < count($paymentTypes); $i++) {
                $data = [
                    'trip_id'      => $trip->id,
                    'driver_id'    => $request->driver_id,
                    'payment_type' => $paymentTypes[$i],
                    'amount'       => $amounts[$i],
                    'date'         => $dates[$i],
                    'comments'     => $comments[$i],
                ];

                if (!empty($paymentIds[$i])) {
                    // Update existing
                    TripPayment::where('id', $paymentIds[$i])->update($data);
                } else {
                    // Insert new
                    TripPayment::create($data);
                }
            }

           $submittedExpenseIds = [];       

            if (!empty($request->expenses)) {
                foreach ($request->expenses as $expenseData) {
                    // Skip incomplete rows
                    if (empty($expenseData['name']) || empty($expenseData['amount'])) {
                        continue;
                    }

                    // Update existing expense
                    if (!empty($expenseData['id'])) {
                        $existing = TripVehicleExpense::find($expenseData['id']);

                        if ($existing) {
                            $existing->update([
                                'expense' => $expenseData['name'],
                                'amount'  => $expenseData['amount'],
                            ]);

                            $submittedExpenseIds[] = $existing->id;
                        }
                    }
                    // Insert new expense
                    else {
                        $new = TripVehicleExpense::create([
                            'trip_id'    => $trip->id,
                            'vehicle_id' => $trip->vehicle_id,
                            'expense'    => $expenseData['name'],
                            'amount'     => $expenseData['amount'],
                        ]);

                        $submittedExpenseIds[] = $new->id;
                    }
                }
            }

           $existingIds = [];
           if ($request->trip_details) {
               foreach ($request->trip_details as $detail) {
                   if (isset($detail['id'])) {
                       // update existing
                       $tripDetail = $trip->tripDetails()->find($detail['id']);
                       if ($tripDetail) {
                           $tripDetail->update($detail);
                           $existingIds[] = $tripDetail->id;
                       }
                   } else {
                       // create new
                       $newDetail = $trip->tripDetails()->create($detail);
                       $existingIds[] = $newDetail->id;
                   }
               }
           }
   
           // delete those which were not sent in request
           $trip->tripDetails()->whereNotIn('id', $existingIds)->delete();
           DB::commit();
           return redirect()->route('admin.trips.index')->with('success', 'Trip updated successfully!');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function endTrip(Request $request) {

        $trip           = TripDetail::findOrFail($request->trip_id);
        $trip->end_date = $request->end_date ?? date('Y-m-d');
        $trip->status   = "Ended";
        $trip->save();

        return response()->json(true);
    }

    public function destroy(Trip $trip)
    {
        $trip->tripDetails()->delete();
        $trip->delete();

        return redirect()->route('admin.trips.index')->with('success', 'Trip deleted successfully!');
    }
}

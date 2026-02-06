<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\TripDetail;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\TripVehicleExpense;
use App\Models\TripPayment;
use App\Models\ExpenseType;
use App\Models\Destination;
use App\Models\PurchaseSheet;
use App\Models\SaleSheet;
use App\Models\Material;
use App\Models\ExpenseFrom;
use App\Models\Customer;
use App\Models\AmountReceivable;
use DB, DataTables;

class TrailerTripController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            if (request()->ajax()) {
            
                $trips = Trip::with('tripDetails', 'vehicle', "vehicle.new_wheeler", 'driver')
                                ->where('status', "Active")
                                ->whereHas('vehicle.new_wheeler', function($q) {
                                    $q->where('name', 'Trailers');
                                });
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

                        $viewUrl   = route('admin.trailer-trips.show', $data->id);
                        $editUrl   = route('admin.trailer-trips.edit', $data->id);
                        $deleteUrl = route('admin.trailer-trips.destroy', $data->id);
                        $endtripUrl = route('admin.endActualTrip', $data->id);


                        return '
                            <a href="'.$viewUrl.'" class="btn btn-sm btn-info">View</a> |
                            <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> |
                            <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form> | 
                            <button type="button"
                                    class="btn btn-sm btn-success tripEndBtn"
                                    data-id="'.$data->id.'"
                                    data-balance="'.$data->balance.'"
                                    data-bs-toggle="modal"
                                    data-bs-target="#endTripModal">
                                End Trip
                            </button>

                        ';
                    })
                    ->rawColumns(['action', 'vehicle', 'driver', 'journey_count'])->make(true);

            }

        } catch (\Exception $ex) {
            return redirect('/')->with('error', $ex->getMessage());
        }

        return view('admin.trips.active_trailers_trips');

        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function show($trip_id)
    {
        $trip = Trip::with('fuelings', 'tripDetails.customer', 'tripDetails.from_dest', 'tripDetails.to_dest', 'vehicle', 'driver', 'tripDetails', 'tripPayments', 'tripExpenses', 'tripExpenses.expenseName')->findOrFail($trip_id);

        return view('admin.trips.detail', compact('trip'));
    }

    public function edit($trip_id)
    {
        $trip          = Trip::with('tripDetails')->findOrFail($trip_id);
        $vehicles      = Vehicle::all();
        $drivers       = Driver::where('status', 'active')->get();
        $expensesTypes = ExpenseType::all();
        $expenses      = TripVehicleExpense::with("expenseName")->where("trip_id", $trip->id)->get();
        $payments      = TripPayment::where("trip_id", $trip->id)->get();
        $destinations  = Destination::all();
        $sales         = SaleSheet::orderByDESC("id")->get();
        $purchases     = PurchaseSheet::orderByDESC("id")->get();
        $materials     = Material::orderBy("name", "ASC")->get();
        $expense_froms = ExpenseFrom::orderBy("name", "ASC")->get();
        $customers     = Customer::all();
        $advance       = TripPayment::where("trip_id", $trip->id)->sum('amount');

        return view('admin.trips.edit', compact("advance", "customers", "expense_froms", "sales", "purchases", "materials", 'trip', 'vehicles', 'drivers', 'expenses', 'payments', 'expensesTypes', 'destinations'));
    }

    public function update(Request $request, $trip_id)
    {
        try {
            $request->validate([
               'vehicle_id' => 'required',
               'driver_id'  => 'required',
            ]);
   
           DB::beginTransaction();

           $trip             = Trip::findOrFail($trip_id);
           $trip->trip_date  = $request->trip_date;
           $trip->vehicle_id = $request->driver_id;
           $trip->total_rent = $request->total_rent;
           $trip->save();

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

    public function endActualTrailerTrip(Request $request) {

        try {
            $trip                    = Trip::findOrFail($request->trip_id_input);
            $trip->trip_end_date     = $request->end_date;
            $trip->total_expense     = $request->total_expense;
            $trip->remaining_balance = $request->remaining_amount;
            $trip->status            = "Ended";
            $trip->save();
    
            return redirect()->route('admin.activeTrailersTrips')->with('success', 'Trailer Trip ended successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Trip $trip)
    {
        $trip->tripDetails()->delete();
        $trip->delete();

        return redirect()->route('admin.trips.index')->with('success', 'Trip deleted successfully!');
    }
}

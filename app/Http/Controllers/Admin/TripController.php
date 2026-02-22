<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Trip,
    TripDetail,
    Vehicle,
    Driver,
    TripVehicleExpense,
    TripPayment,
    ExpenseType,
    Destination,
    PurchaseSheet,
    SaleSheet,
    Material,
    ExpenseFrom,
    Customer,
    AmountReceivable,
};


use DB, DataTables;

class TripController extends Controller
{
    public function index()
    {
        try {
            if (request()->ajax()) {

                $trips = Trip::with('tripDetails', 'vehicle', "vehicle.new_wheeler", 'driver')
                                ->where('status', "Active")
                                ->whereHas('vehicle.new_wheeler', function($q) {
                                    $q->where('name', '!=',  'Trailers');
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
                            return $data->vehicle->vehicle_no ?? "";
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

                        $viewUrl    = route('admin.trips.show', $data->id);
                        $editUrl    = route('admin.trips.edit', $data->id);
                        $deleteUrl  = route('admin.trips.destroy', $data->id);
                        $endtripUrl = route('admin.endActualTrip', $data->id);
                        
                        
                        // <a href="'.$endtripUrl.'" class="btn btn-sm btn-success endTripBtn">End Trip</a>

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
                            </button>';
                    })
                    ->rawColumns(['action', 'vehicle', 'driver', 'journey_count'])->make(true);

            }

        } catch (\Exception $ex) {
            return redirect('/')->with('error', $ex->getMessage());
        }

        return view('admin.trips.index');
    }

    // public function activeTrailersTrips()
    // {
    //     try {
    //         if (request()->ajax()) {
            
    //             $trips = Trip::with('tripDetails', 'vehicle', "vehicle.new_wheeler", 'driver')
    //                             ->where('status', "Active")
    //                             ->whereHas('vehicle.new_wheeler', function($q) {
    //                                 $q->where('name', 'Trailers');
    //                             });
    //             return datatables()->eloquent($trips->orderByDesc('id'))

    //                 ->filterColumn('vehicle', function($query, $keyword) {
    //                     $query->whereHas('vehicle', function($q) use ($keyword) {
    //                         $q->where('vehicle_no', 'like', "%{$keyword}%");
    //                     });
    //                 })

    //                 ->filterColumn('driver', function($query, $keyword) {
    //                     $query->whereHas('driver', function($q) use ($keyword) {
    //                         $q->where('name', 'like', "%{$keyword}%");
    //                     });
    //                 })


    //                 ->addColumn('vehicle', function ($data) {
    //                     if($data->vehicle != null) {
    //                         return $data->vehicle->vehicle_no;
    //                     } else {
    //                         return "";
    //                     }
    //                 })
    //                 ->editColumn('trip_date', function ($data) {
                       
    //                     return date("d-m-Y", strtotime($data->trip_date));
                        
    //                 })

                    
    //                 ->addColumn('driver', function ($data) {
    //                     if($data->driver != null) {
    //                         return $data->driver->name;
    //                     } else {
    //                         return "";
    //                     }
    //                 })
    //                 ->addColumn('journey_count', function ($data) {
    //                     return  $data->tripDetails->count() ?? 0;
    //                 })
    //                 ->editColumn('created_at', function ($data) {
    //                     return  date('d M Y', strtotime($data->created_at));
    //                 })   
    //                 ->addColumn('action', function ($data) {

    //                     $viewUrl   = route('admin.trips.show', $data->id);
    //                     $editUrl   = route('admin.trips.edit', $data->id);
    //                     $deleteUrl = route('admin.trips.destroy', $data->id);
    //                     $endtripUrl = route('admin.endActualTrip', $data->id);


    //                     return '
    //                         <a href="'.$viewUrl.'" class="btn btn-sm btn-info">View</a> |
    //                         <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> |
    //                         <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
    //                             '.csrf_field().'
    //                             '.method_field('DELETE').'
    //                             <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
    //                         </form> | 
    //                         <button type="button"
    //                                 class="btn btn-sm btn-success tripEndBtn"
    //                                 data-id="'.$data->id.'"
    //                                 data-balance="'.$data->balance.'"
    //                                 data-bs-toggle="modal"
    //                                 data-bs-target="#endTripModal">
    //                             End Trip
    //                         </button>

    //                     ';
    //                 })
    //                 ->rawColumns(['action', 'vehicle', 'driver', 'journey_count'])->make(true);

    //         }

    //     } catch (\Exception $ex) {
    //         return redirect('/')->with('error', $ex->getMessage());
    //     }

    //     return view('admin.trips.active_trailers_trips');
    // }
    public function closedTrailersTrips()
    {
        try {
            
            if (request()->ajax()) {
                $trips = Trip::with('tripDetails', 'vehicle', "vehicle.new_wheeler", 'driver')
                                ->where('status', "Ended")
                                ->whereHas('vehicle.new_wheeler', function($q) {
                                    $q->where('name', 'Trailers');
                                });
            
                $dataTable = datatables()->eloquent($trips->orderByDesc('id'))

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
                    ->editColumn('trip_end_date', function ($data) {
                        return  date('d-m-Y', strtotime($data->trip_end_date));
                    });   
                    
                // Add action column only for role_id == 1
                if(auth()->user()->role_id == 1) {

                    $dataTable->addColumn('action', function ($data) {
                        $viewUrl    = route('admin.trips.show', $data->id);
                        $editUrl    = route('admin.trips.edit', $data->id);
                        $deleteUrl  = route('admin.trips.destroy', $data->id);

                        return '
                            <a href="'.$viewUrl.'" class="btn btn-sm btn-info">View</a> |
                            <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> | 
                            <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                    })->rawColumns(['action', 'vehicle', 'driver', 'journey_count']);
                } else {
                    $dataTable->rawColumns(['vehicle', 'driver', 'journey_count']);
                }

                return $dataTable->make(true);
            }

        } catch (\Exception $ex) {
            return redirect('/')->with('error', $ex->getMessage());
        }

        return view('admin.trips.ended_trailers_trips');
    }
    
    public function closedTrips() {
        try {
            if (request()->ajax()) {
            
                $trips = Trip::with('tripDetails', 'vehicle', 'vehicle.new_wheeler', 'driver')
                                ->where('status', "Ended")
                                ->whereHas('vehicle.new_wheeler', function($q) {
                                    $q->where('name', '!=',  'Trailers');
                                });
                
                $dataTable = datatables()->eloquent($trips->orderByDesc('id'))

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
                        return $data->vehicle->vehicle_no ?? '';
                    })
                    
                    ->editColumn('trip_date', function ($data) {
                        return date("d-m-Y", strtotime($data->trip_date));
                    })
                    ->editColumn('trip_end_date', function ($data) {
                        return date("d-m-Y", strtotime($data->trip_end_date));
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
                    });

                // Add action column only for role_id == 1
                if(auth()->user()->role_id == 1) {
                    $dataTable->addColumn('action', function ($data) {
                        $viewUrl    = route('admin.trips.show', $data->id);
                        $editUrl    = route('admin.trips.edit', $data->id);
                        $deleteUrl  = route('admin.trips.destroy', $data->id);

                        
                        return '
                            <a href="'.$viewUrl.'" class="btn btn-sm btn-info">View</a> |
                            <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> | 
                            <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                    })->rawColumns(['action', 'vehicle', 'driver', 'journey_count']);
                } else {
                    $dataTable->rawColumns(['vehicle', 'driver', 'journey_count']);
                }

                return $dataTable->make(true);
            }

        } catch (\Exception $ex) {
            return redirect('/')->with('error', $ex->getMessage());
        }

        return view('admin.trips.closed_trips');
    }

    public function create()
    {
        $vehicles      = Vehicle::all();
        $drivers       = Driver::where('status', 'active')->get();
        $expenses      = ExpenseType::all();
        $destinations  = Destination::all();
        $sales         = SaleSheet::orderByDESC("id")->get();
        $purchases     = PurchaseSheet::orderByDESC("id")->get();
        $materials     = Material::orderBy("name", "ASC")->get();
        $expense_froms = ExpenseFrom::orderBy("name", "ASC")->get();
        $customers     = Customer::all();

        
        return view('admin.trips.create', compact("customers", "expense_froms", "materials", 'vehicles', 'drivers', "expenses", "destinations", "sales", "purchases"));
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
                                // 'trip_no'    => $trip_no,
                                // "trip_type"  => $request->trip_type,
                                'vehicle_id' => $request->vehicle_id,
                                'driver_id'  => $request->driver_id,
                                "balance"    => $request->balance,
                                "total_rent" => $request->total_rent,
                                "trip_date"  => $request->trip_date,
                                "status"     => "Active"
                            ]);


            $trip->trip_no = $trip->id;
            $trip->save();
            
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
                    $expenseTypeId = ExpenseType::where("name", $expenseData['name'])->pluck("id")->first();
                    TripVehicleExpense::create([
                        'trip_id'         => $trip->id,
                        'vehicle_id'      => $request->vehicle_id,
                        "expense_type_id" => $expenseTypeId ?? 0,
                        'expense'         => $expenseData['name'],
                        'expense_from'    => $expenseData["expense_from"],
                        'amount'          => $expenseData['amount'],
                    ]);
                }
            }
    
            // Save trip details
            if ($request->trip_details) {
                foreach ($request->trip_details as $detail) {

                    TripDetail::create([
                        "trip_id"           => $trip->id,
                        "customer_id"       => $detail['customer_id'] ?? null,
                        "trip_type"         => $detail['trip_type'] ?? null,
                        "start_date"        => $detail['start_date'] ?? null,
                        "end_date"          => $detail['end_date'] ?? null,
                        "from_destination"  => $detail['from_destination'] ?? null,
                        "to_destination"    => $detail['to_destination'] ?? null,
                        "material"          => $detail['material'] ?? null,
                        "material_type"     => $detail['material_type'] ?? null,
                        "total_bags"        => $detail['total_bags'] ?? 0,
                        "weekly_labour"     => $detail['weekly_labour'] ?? 0,
                        "baloch_labour"     => $detail['baloch_labour'] ?? 0,
                        "baloch_labour_rate"=> $detail['baloch_labour_rate'] ?? 0,
                        "no_of_labour"      => $detail['no_of_labour'] ?? 0,
                        "rent"              => $detail['rent'] ?? 0,
                        "is_payment_receive" => $detail['payment_receive'] ?? 'No',
                        "receive_amount"     => $detail['receive_amount'] ?? 0,
                        "receive_by"         => $detail['receive_by'] ?? null,
                        "comments"          => $detail['comments'] ?? null,
                        "weight"            => $detail['weight'] ?? 0,
                    ]);

                    $customer = Customer::findOrFail($detail['customer_id']);
                    if($detail['payment_receive'] == "Yes") {
                        $rem_amount = $detail['rent'] - $detail['receive_amount'];
                        $customer->outstanding_amount += $rem_amount;

                        AmountReceivable::create([
                            'trip_id'     => $trip->id,
                            'customer_id' => $detail['customer_id'],
                            'amount'      => $detail['receive_amount'],
                            "date"        => date("Y-m-d"),
                            'receipt'     => ""
                        ]);
                    } else {
                        $customer->outstanding_amount += $detail['rent'];
                    }
                    $customer->save();

                    
                    // $trip->tripDetails()->create($detail);
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
        $trip->load(['fuelings', 'tripDetails.customer', 'tripDetails.from_dest', 'tripDetails.to_dest', 'vehicle', 'driver', 'tripDetails', 'tripPayments', 'tripExpenses', 'tripExpenses.expenseName']);
        return view('admin.trips.detail', compact('trip'));
    }

    public function edit(Trip $trip)
    {
        $trip->load(['tripDetails' => function($query) {
            $query->whereNull('end_date');
        }]);
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

    public function update(Request $request, Trip $trip)
    {
        try {
            $request->validate([
               'vehicle_id' => 'required',
               'driver_id'  => 'required',
            ]);
   
           DB::beginTransaction();

           $trip->update($request->only('trip_no', 'trip_date', 'vehicle_id', 'driver_id', 'total_rent', 'balance'));

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
                            $expenseTypeId = ExpenseType::where("name", $expenseData['name'])->pluck("id")->first();
                            $existing->update([
                                'expense'         => $expenseData['name'],
                                'amount'          => $expenseData['amount'],
                                "expense_type_id" => $expenseTypeId ?? 0,
                                'expense_from'    => $expenseData['expense_from'],
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
                            'expense_from'  => $expenseData['expense_from'],
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

    public function endActualTrip(Request $request) {

        try {
            //code...
            $trip                    = Trip::findOrFail($request->trip_id_input);
            $trip->trip_end_date     = $request->end_date;
            $trip->total_expense     = $request->total_expense;
            $trip->remaining_balance = $request->remaining_amount;
            $trip->status            = "Ended";
            $trip->save();
    
            return redirect()->route('admin.trips.index')->with('success', 'Trip ended successfully!');
        } catch (\Exception $e) {
            //throw $th;
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

    public function disbursement_slip(Request $request)
    {
        $trip = null;
        $advance_amount = null;
        if ($request->filled('trip_id')) {
            $trip = Trip::with('tripDetails', 'vehicle', 'driver', 'tripPayments')
                            ->where('id', $request->trip_id)
                            ->where('status', 'Active')
                            ->first();

            $advance_amount = TripPayment::where('trip_id', $request->trip_id)->sum('amount');
        }

        return view('admin.trips.disbursement_slip', compact('trip', 'advance_amount'));
    }

    public function deleteExpense($id)
    {
        $expense = TripVehicleExpense::findOrFail($id);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully'
        ]);
    }

    public function deleteTripDetail($id)
    {
        $detail = TripDetail::findOrFail($id);
        $detail->delete();

        return response()->json([
            'success' => true,
            'message' => 'Trip detail deleted'
        ]);
    }


}

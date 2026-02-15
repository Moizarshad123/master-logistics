<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Diesel;
use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\TripVehicleExpense;
use App\Models\Setting;
use Carbon\Carbon;
use DB, Auth;

class DieselController extends Controller
{
    public function index()
    {
        try {
            if (request()->ajax()) {
                $diesels = Diesel::with("user");
                return datatables()->eloquent($diesels->orderByDesc('id'))
                    ->addColumn('createdBy', function ($data) {
                        if($data->created_by != null) {
                            return $data->user->name;
                        } else {
                            return "";
                        }
                    })
                    ->addColumn('dateTime', function ($data) {
                        return  date('d M Y', strtotime($data->date)).' - '.date('H:i A', strtotime($data->time));
                    }) 
                     ->addColumn('vehicle', function ($data) {
                        if($data->vehicle_id != null) {
                            return $data->vehicle->vehicle_no;
                        } else {
                            return "";
                        }
                    })   
                    
                    ->addColumn('action', function ($data) {

                        $editUrl    = route('admin.diesel.edit', $data->id);
                        $deleteUrl  = route('admin.diesel.destroy', $data->id);

                        return '
                            <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> |
                            <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                    })
                    ->filterColumn('vehicle', function($query, $keyword) {
                        $query->whereHas('vehicle', function($q) use ($keyword) {
                            $q->where('vehicle_no', 'like', "%{$keyword}%");
                        });
                    })
                    ->filterColumn('createdBy', function($query, $keyword) {
                        $query->whereHas('user', function($q) use ($keyword) {
                            $q->where('name', 'like', "%{$keyword}%");
                        });
                    })
                    ->rawColumns(['action', 'createdBy', 'dateTime', 'vehicle'])->make(true);

            }
        } catch (\Exception $ex) {
            return redirect('admin/diesel')->with('error', $ex->getMessage());
        }

        
        return view('admin.diesel.index');
    }

    public function create()
    {
        $vehicles = Vehicle::all();
        $trips    = Trip::Select("id")->orderBy('id', 'ASC')->get();
        return view('admin.diesel.create', compact("vehicles", "trips"));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'type'             => 'required',
                'date'             => 'required|date',
                'time'             => 'required',
                'litres'           => 'required|numeric',
                'per_litre_amount' => 'required|numeric',
                'total_amount'     => 'required|numeric',
                'source'           => 'required',

            ]);
    
            DB::beginTransaction();
            Diesel::create([
                'vehicle_id'       => $request->vehicle_id,
                'trip_id'          => $request->trip_id,
                'type'             => $request->type,
                'date'             => $request->date,
                'time'             => $request->time,
                'litres'           => $request->litres,
                'per_litre_amount' => $request->per_litre_amount,
                'total_amount'     => $request->total_amount,
                'created_by'       => Auth::id(),
                'source'           => $request->source,
            ]);

            if($request->trip_id != null) {
                TripVehicleExpense::create([
                    'trip_id'      => $request->trip_id,
                    'vehicle_id'   => $request->vehicle_id,
                    'expense'      => "Diesel",
                    'expense_from' => "From Advance Amount",
                    'amount'       => $request->total_amount,
                ]);
            }

            if($request->source == "Master Sweetner") {
                $setting = Setting::findOrFail(1);
                if($request->type == "Petrol") {
                    $setting->total_petrol -= $request->litres;
                } elseif($request->type == "Diesel") {
                    $setting->total_diesel -= $request->litres;
                }   
                $setting->save();
            }   
            DB::Commit();
    
            return redirect()->route('admin.diesel.index')->with('success', 'Record created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        $diesel = Diesel::findOrFail($id);
        return view('admin.diesel.show', compact('diesel'));
    }

    public function edit($id)
    {
        $vehicles = Vehicle::all();
        $trips    = Trip::Select("id")->orderBy('id', 'ASC')->get();

        $diesel = Diesel::findOrFail($id);
        return view('admin.diesel.edit', compact('diesel', 'vehicles', 'trips'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'vehicle_id'       => 'required',
            'type'             => 'required',
            'date'             => 'required|date',
            'time'             => 'required',
            'litres'           => 'required|numeric',
            'per_litre_amount' => 'required|numeric',
            'total_amount'     => 'required|numeric',
            'source'           => 'required',
        ]);

        $diesel = Diesel::findOrFail($id);
        $diesel->update($request->all());

        return redirect()->route('admin.diesel.index')->with('success', 'Record updated successfully.');
    }

    public function destroy($id)
    {
        Diesel::findOrFail($id)->delete();
        return back()->with('success', 'Record deleted successfully.');
    }

    public function fuelConsumptionReport(Request $request) {
        $from = $request->from_date ?? Carbon::today()->toDateString();
        $to   = $request->to_date   ?? Carbon::today()->toDateString();

        $records = Diesel::with('vehicle')
            ->whereBetween('date', [$from, $to])
            ->selectRaw('
                vehicle_id,
                type,
                source,
                SUM(litres) as total_litres,
                SUM(total_amount) as total_amount,
                MAX(id) as latest_id
            ')
            ->groupBy('vehicle_id', 'type', 'source')
            ->get();

        // latest per litre amount
        $records->map(function ($row) {
            $latest = Diesel::find($row->latest_id);
            $row->per_litre_amount = $latest->per_litre_amount ?? 0;
            return $row;
        });

        return view('admin.diesel.fuel_consumption_report', compact('records', 'from', 'to'));
    }
}

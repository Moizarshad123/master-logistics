<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripDetail;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\TripVehicleExpense;
use Illuminate\Http\Request;
use DB;
class TripController extends Controller
{
    public function index()
    {
        $trips = Trip::with('tripDetails', 'vehicle', 'driver')->latest()->paginate(10);
        return view('admin.trips.index', compact('trips'));
    }

    public function create()
    {
        $vehicles = Vehicle::all();
        $drivers  = Driver::all();
        return view('admin.trips.create', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'vehicle_id' => 'required',
                'driver_id'  => 'required',
            ]);
    
            DB::beginTransaction();
    
            $trip_no = str_pad(Trip::max('id') + 1, 6, '0', STR_PAD_LEFT);
    
            $trip = Trip::create([
                'trip_no' => $trip_no,
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
            ]);

            if(count($request['expenses']) > 0) {
                foreach ($request['expenses'] as $expenseData) {
                    TripVehicleExpense::create([
                        'trip_id'    => $trip->id,
                        'vehicle_id' => $request->vehicle_id,
                        'expense'    => $expenseData['expense_type_id'],
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
        $trip->load(['vehicle', 'driver', 'tripDetails', 'tripExpenses', 'tripExpenses.expenseName']);
        return view('admin.trips.detail', compact('trip'));
    }

    public function edit(Trip $trip)
    {
        $trip->load('tripDetails');
        $vehicles = Vehicle::all();
        $drivers  = Driver::all();
        $expenses = TripVehicleExpense::with("expenseName")->where("trip_id", $trip->id)->get();
        return view('admin.trips.edit', compact('trip', 'vehicles', 'drivers', 'expenses'));
    }

    public function update(Request $request, Trip $trip)
    {
        try {
           $request->validate([
               'trip_no'    => 'required|unique:trips,trip_no,' . $trip->id,
               'vehicle_id' => 'required|exists:vehicles,id',
               'driver_id'  => 'required|exists:drivers,id',
           ]);
   
           DB::beginTransaction();

           $trip->update($request->only('trip_no', 'vehicle_id', 'driver_id'));
   
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


    public function destroy(Trip $trip)
    {
        $trip->tripDetails()->delete();
        $trip->delete();

        return redirect()->route('admin.trips.index')->with('success', 'Trip deleted successfully!');
    }
}

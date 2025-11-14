<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\Vehicle;
use App\Models\ExpenseType;

use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
   public function index()
    {
        $maintenances = Maintenance::with('vehicle', 'expense')->get();
        return view('admin.maintenances.index', compact('maintenances'));
    }

    public function create()
    {
        $expenses = ExpenseType::all();
        $vehicles = Vehicle::all();
        return view('admin.maintenances.create', compact('vehicles', "expenses"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'amount' => 'required|numeric',
            'comments' => 'nullable|string',
        ]);

        Maintenance::create($request->only('vehicle_id', 'expense_id', 'amount', 'comments'));

        return redirect()->route('admin.maintenances.index')->with('success', 'Maintenance added successfully.');
    }

    public function edit(Maintenance $maintenance)
    {
        $vehicles = Vehicle::all();
        $expenses = ExpenseType::all();

        return view('admin.maintenances.edit', compact('maintenance', 'vehicles', "expenses"));
    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'expense_id' => 'required',
            'amount'     => 'required|numeric',
            'comments'   => 'nullable|string',
        ]);

        $maintenance->update($request->only('vehicle_id', 'expense_id', 'amount', 'comments'));

        return redirect()->route('admin.maintenances.index')->with('success', 'Maintenance updated successfully.');
    }

    public function destroy(Maintenance $maintenance)
    {
        $maintenance->delete();
        return redirect()->route('admin.maintenances.index')->with('success', 'Maintenance deleted successfully.');
    }
}

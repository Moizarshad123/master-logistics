<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FuelSupplier;
use Illuminate\Http\Request;
use App\Models\AmountPayable;

class FuelSupplierController extends Controller
{
    public function index()
    {
        $fuelSuppliers = FuelSupplier::latest()->get();
        return view('admin.fuel-suppliers.index', compact('fuelSuppliers'));
    }

    public function create()
    {
        return view('admin.fuel-suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        FuelSupplier::create($request->only('name'));

        return redirect()->route('admin.fuel-suppliers.index')
            ->with('success', 'Fuel Supplier Created Successfully');
    }

    public function edit(FuelSupplier $fuelSupplier)
    {
        return view('admin.fuel-suppliers.edit', compact('fuelSupplier'));
    }

    public function show($fuelSupplierId)
    {
        $history = AmountPayable::where("supplier_id", $fuelSupplierId)->orderByDESC("id")->get();
        return view('admin.fuel-suppliers.show', compact('history'));
    }

    public function update(Request $request, FuelSupplier $fuelSupplier)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $fuelSupplier->update($request->only('name'));

        return redirect()->route('admin.fuel-suppliers.index')
            ->with('success', 'Fuel Supplier Updated Successfully');
    }

    public function destroy(FuelSupplier $fuelSupplier)
    {
        $fuelSupplier->delete();

        return redirect()->route('admin.fuel-suppliers.index')
            ->with('success', 'Fuel Supplier Deleted Successfully');
    }
}

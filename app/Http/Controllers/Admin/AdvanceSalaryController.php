<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdvanceSalary;

class AdvanceSalaryController extends Controller
{
    public function index()
    {
        return AdvanceSalary::with('driver')->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'driver_id' => 'required',
            'month'     => 'required',
            'year'      => 'required',
            'amount'    => 'required|numeric'
        ]);

        AdvanceSalary::create($request->all());

        return response()->json(['message' => 'Advance salary added']);
    }

    public function destroy($id)
    {
        AdvanceSalary::findOrFail($id)->delete();
        return response()->json(['message' => 'Advance deleted']);
    }
}

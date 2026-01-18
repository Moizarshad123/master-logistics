<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdvanceSalary;
use App\Models\Driver;

class AdvanceSalaryController extends Controller
{
    public function index()
    {
        $advance = AdvanceSalary::with('driver')->latest()->get();
        return view('admin.payroll.advance.index', compact('advance'));
    }

    public function create() {
        $drivers = Driver::where('status', 'active')->orderBy("name", "ASC")->get();
        return view("admin.payroll.advance.create", compact("drivers"));
    }

    public function store(Request $request)
    {

        $request->validate([
            'driver_id' => 'required',
            'month'     => 'required',
            'amount'    => 'required|numeric'
        ]);
        $monthYear      = $request->month;
        [$year, $month] = explode('-', $monthYear);
        AdvanceSalary::create([
                                'driver_id' => $request->driver_id,
                                'month'     => $month,
                                'year'      => $year,
                                'amount'    => $request->amount,
                                "status"    => "Amount Due"
                            ]);
    
        return redirect('admin/advance-salaries')->with("success", 'Advance salary added');
    }

    public function edit($id) {
        $drivers = Driver::where('status', 'active')->orderBy("name", "ASC")->get();
        $salary = AdvanceSalary::findOrFail($id);

        return view("admin.payroll.advance.edit", compact("drivers", 'salary'));
    }

    public function update(Request $request, $id) {

        $request->validate([
            'driver_id' => 'required',
            'month'     => 'required',
            'amount'    => 'required|numeric'
        ]);
        $monthYear      = $request->month;
        [$year, $month] = explode('-', $monthYear);
        $salary            = AdvanceSalary::findOrFail($id);
        $salary->driver_id = $request->driver_id;
        $salary->month     = $month;
        $salary->year      = $year;
        $salary->amount    = $request->amount;
        $salary->status    = $request->status;
        $salary->save();
    
        return redirect('admin/advance-salaries')->with("success", 'Advance salary updated');
    }

    public function destroy($id)
    {
        AdvanceSalary::findOrFail($id)->delete();
        return response()->json(['message' => 'Advance deleted']);
    }
}

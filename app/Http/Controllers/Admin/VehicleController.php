<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\ExpenseType;
use App\Models\Wheeler;

use DB;

class VehicleController extends Controller
{

    public function getVehicleExpenses(Request $request)
    {
        $vehicleId = $request->vehicle_id;

        $expenses = Vehicle::with('expenseTypes')->findOrFail($request->vehicle_id);
        $html = "";
        if(count($expenses->expenseTypes) > 0) {
            $html .= ' <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Expense</th>
                    <th>Expense Amount</th>
                </tr>
            </thead>
            <tbody>';
            foreach ($expenses->expenseTypes as $expense) {
                $html .= '<tr>
                    <td>' . e($expense->name) . '</td>
                    <td>
                        <input type="number" step="0.01" 
                            name="expenses['.$expense->id.'][amount]" 
                            class="form-control" 
                            placeholder="Enter amount">
                        <input type="hidden" 
                            name="expenses['.$expense->id.'][expense_type_id]" 
                            value="'.$expense->id.'">
                    </td>
                </tr>';
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<p>No expenses found for this vehicle.</p>';
        }

        return response()->json($html);
    }

    public function index()
    {
        $vehicles = Vehicle::with("wheeler")->orderByDESC("id")->paginate(10);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    public function expenses($id)
    {
        $vehicle = Vehicle::with('expenseTypes')->findOrFail($id);
        $allExpenses = ExpenseType::all(); // for add/edit dropdown
        return view('admin.vehicles.expenses', compact('vehicle', 'allExpenses'));
    }

    public function storeExpense(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
        ]);

        $vehicle->expenseTypes()->attach($request->expense_type_id);

        return back()->with('success', 'Expense added successfully!');
    }

    public function updateExpense(Request $request, Vehicle $vehicle, ExpenseType $expense)
    {
        $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
        ]);

        // detach old, attach new
        $vehicle->expenseTypes()->detach($expense->id);
        $vehicle->expenseTypes()->attach($request->expense_type_id);

        return back()->with('success', 'Expense updated successfully!');
    }

    public function deleteExpense(Vehicle $vehicle, ExpenseType $expense)
    {
        $vehicle->expenseTypes()->detach($expense->id);

        return back()->with('success', 'Expense deleted successfully!');
    }


    public function create()
    {

        $wheelers = Wheeler::all();
        $expenses = ExpenseType::all();
        return view('admin.vehicles.create', compact('expenses', "wheelers"));
    }

    public function store(Request $request)
    {

        try {
            $request->validate([
                'vehicle_no'  => 'required|unique:vehicles',
            ]);
    
            $data = $request->all();
            $dir   = "uploads/drivers/";
    
            DB::beginTransaction();
    
            if ($request->hasFile('image')) {
                $file     = $request->file('image');
                $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
                $file->move($dir, $fileName);
                $fileName = $dir.$fileName;
    
                $data['image'] = asset($fileName);
            }
    
            $vehicle = Vehicle::create($data);
            if ($request->has('expense_types')) {
                $vehicle->expenseTypes()->attach($request->expense_types);
            }
            DB::commit();
    
            return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle added successfully.');
        } catch (\Exception $e) {
            DB::rollback();

            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Vehicle $vehicle)
    {
        $wheelers = Wheeler::all();
        $expenses = ExpenseType::all();
        return view('admin.vehicles.edit', compact('vehicle', 'expenses', "wheelers"));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'vehicle_no'  => 'required',
            'vehicle_type'=> 'required'
        ]);

        $data = $request->all();

        $dir   = "uploads/drivers/";

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $fileName = $dir.$fileName;

            $data['image'] = asset($fileName);
        }

        $vehicle->update($data);

        if ($request->has('expense_types')) {
            $vehicle->expenseTypes()->sync($request->expense_types);
        }

        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->route('admin.vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}

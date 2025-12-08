<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\ExpenseType;
use App\Models\Wheeler;
use App\Models\ExpenseFrom;
use DB;

class VehicleController extends Controller
{

    public function getVehicleExpenses(Request $request)
    {
        $vehicleId     = $request->vehicle_id;
        $expenses      = Vehicle::with('expenseTypes')->findOrFail($request->vehicle_id);
        $expense_froms = ExpenseFrom::orderBy("name", "ASC")->get();
        $html = "";
        // <td>' . e($expense->name) . '</td>
        if(count($expenses->expenseTypes) > 0) {
            $html .= ' <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Expense</th>
                    <th>Expense Amount</th>
                    <th>Expense From</th>
                    <th> <button type="button" class="btn btn-sm btn-warning" id="addExpenseRow" style="display: none">
                        + Add More Expenses
                    </button></th>
                </tr>
            </thead>
            <tbody id="expensesTableBody">';
            foreach ($expenses->expenseTypes as $expense) {

                $new = '<select name="expenses['.$expense->id.'][expense_from]" class="form-select">
                    <option value="">Select Expense From</option>';

                foreach ($expense_froms as $item) {
                    $new .= '<option value="' . $item->name . '">' . $item->name . '</option>';
                }

                $new .= '</select>';
        
                $html .= '<tr>
                    <td>
                     <input type="text" 
                                name="expenses[${extraExpenseIndex}][name]" 
                                class="form-control" value="'.e($expense->name).'" readonly>
                    </td>
                    <td>
                        <input type="hidden" name="expenses['.$expense->id.'][name]" value="'.$expense->name.'">
                        <input type="number" step="0.01"  name="expenses['.$expense->id.'][amount]" 
                            class="form-control" placeholder="Enter amount">
                        
                    </td>
                    <td>'.$new.'

                        </td>
                    <td></td>
                </tr>';
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<table class="table table-bordered">
            <thead>
                <tr>
                    <th>Expense</th>
                    <th>Expense Amount</th>
                    <th>Expense From</th>
                    <th> <button type="button" class="btn btn-sm btn-warning" id="addExpenseRow" style="display: none">
                        + Add More Expenses
                    </button></th>
                </tr>
            </thead> 
            <tbody id="expensesTableBody"></tbody></table>';
        }

        return response()->json($html);
    }

    public function index()
    {

        try {
            if (request()->ajax()) {

                $vehicles = Vehicle::with("wheeler");
                return datatables()->eloquent($vehicles->orderByDesc('id'))
                    ->addColumn('vehicleImage', function ($data) {
                        return '<img src="'.$data->image.'" width="100" height="100" style="border-radius: 50%">';
                        
                    })
                    ->addColumn('wheeler', function ($data) {
                        if($data->wheeler != null) {
                            return $data?->wheeler?->name;
                        } else {
                            return "";
                        }
                    })
                    // ->editColumn('created_at', function ($data) {
                    //     return  date('d M Y', strtotime($data->created_at));
                    // })   
                    ->addColumn('action', function ($data) {

                        $viewUrl    = route('admin.vehicles.show', $data->id);
                        $editUrl    = route('admin.vehicles.edit', $data->id);
                        $deleteUrl  = route('admin.vehicles.destroy', $data->id);

                        return '
                            <a href="'.$viewUrl.'" class="btn btn-sm btn-info">View</a> |
                            <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> |
                            <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                    })
                    ->rawColumns(['action', 'vehicleImage', 'wheeler'])->make(true);

            }
        } catch (\Exception $ex) {
            return redirect('admin/vehicles')->with('error', $ex->getMessage());
        }

        return view('admin.vehicles.index');
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
                $fileName = time() . '-' . uniqid() . '-vehicle.' . $file->getClientOriginalExtension();
                $file->move($dir, $fileName);
                $fileName = $dir.$fileName;
    
                $data['image'] = asset($fileName);
            }

            if ($request->hasFile('route_permit_sindh')) {
                $file     = $request->file('route_permit_sindh');
                $fileName = time() . '-' . uniqid() . '-vehicle.' . $file->getClientOriginalExtension();
                $file->move($dir, $fileName);
                $fileName = $dir.$fileName;
    
                $data['route_permit_sindh'] = asset($fileName);
            }
            if ($request->hasFile('route_permit_punjab')) {
                $file     = $request->file('route_permit_punjab');
                $fileName = time() . '-' . uniqid() . '-vehicle.' . $file->getClientOriginalExtension();
                $file->move($dir, $fileName);
                $fileName = $dir.$fileName;
    
                $data['route_permit_punjab'] = asset($fileName);
            }
            if ($request->hasFile('fitness_certificate')) {
                $file     = $request->file('fitness_certificate');
                $fileName = time() . '-' . uniqid() . '-vehicle.' . $file->getClientOriginalExtension();
                $file->move($dir, $fileName);
                $fileName = $dir.$fileName;
    
                $data['fitness_certificate'] = asset($fileName);
            }
            if ($request->hasFile('insurance_certificate')) {
                $file     = $request->file('insurance_certificate');
                $fileName = time() . '-' . uniqid() . '-vehicle.' . $file->getClientOriginalExtension();
                $file->move($dir, $fileName);
                $fileName = $dir.$fileName;
    
                $data['insurance_certificate'] = asset($fileName);
            }
            if ($request->hasFile('tax_token')) {
                $file     = $request->file('tax_token');
                $fileName = time() . '-' . uniqid() . '-vehicle.' . $file->getClientOriginalExtension();
                $file->move($dir, $fileName);
                $fileName = $dir.$fileName;
    
                $data['tax_token'] = asset($fileName);
            }
            if ($request->hasFile('vehicle_file')) {
                $file     = $request->file('vehicle_file');
                $fileName = time() . '-' . uniqid() . '-vehicle.' . $file->getClientOriginalExtension();
                $file->move($dir, $fileName);
                $fileName = $dir.$fileName;
    
                $data['vehicle_file'] = asset($fileName);
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
        $vehicle  = Vehicle::with("wheeler")->findOrFail($id);
        return view('admin.vehicles.show', compact('vehicle'));
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

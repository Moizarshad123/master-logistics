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
        try {
            if (request()->ajax()) {
                $maintenances = Maintenance::with('expense');
                return datatables()->eloquent($maintenances->orderByDesc('id'))
                   
                    ->editColumn('expense', function ($data) {
                        if($data->expense != null) {
                            return $data->expense->name;
                        } else {
                            return "";
                        }
                    })
                    ->editColumn('created_at', function ($data) {
                        return  date('d M Y', strtotime($data->created_at));
                    })   
                    ->addColumn('action', function ($data) {

                        $viewUrl    = route('admin.maintenances.show', $data->id);
                        $editUrl    = route('admin.maintenances.edit', $data->id);
                        $deleteUrl  = route('admin.maintenances.destroy', $data->id);

                        if(auth()->user()->role_id == 2) {
                            return '<form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                        '.csrf_field().'
                                        '.method_field('DELETE').'
                                        <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                                    </form>';

                        } else {
                            return '<a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> |
                                    <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                        '.csrf_field().'
                                        '.method_field('DELETE').'
                                        <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                                    </form>';

                        }

                        
                    })
                    ->rawColumns(['action', 'expense'])->make(true);

            }
        } catch (\Exception $ex) {
            return redirect('admin/maintenances')->with('error', $ex->getMessage());
        }

        return view('admin.maintenances.index');
    }

    public function create()
    {
        $expenses = ExpenseType::all();
        $vehicles = Vehicle::all();
        return view('admin.maintenances.create', compact('vehicles', "expenses"));
    }

    public function store(Request $request)
    {
        try {
            //code...
            $request->validate([
                'vehicle_id' => 'required',
                'amount'     => 'required|numeric',
                'comments'   => 'nullable|string',
            ]);
    
            Maintenance::create($request->only('vehicle_id', 'expense_id', 'amount', 'comments'));
            return redirect()->route('admin.maintenances.index')->with('success', 'Maintenance added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
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

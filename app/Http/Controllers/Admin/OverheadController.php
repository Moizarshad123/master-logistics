<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Overhead;
use App\Models\ExpenseType;
use App\Models\Driver;
use Illuminate\Http\Request;

class OverheadController extends Controller
{
    public function index()
    {
        try {
            if (request()->ajax()) {
                $overheads = Overhead::with(['expenseType', 'driver'])->latest();

                return datatables()->eloquent($overheads)
                    ->addIndexColumn()
                    ->addColumn('expense_type_name', function ($row) {
                        return $row->expenseType->name ?? '—';
                    })
                    ->addColumn('driver_name', function ($row) {
                        return $row->driver ? $row->driver->name : '—';
                    })
                    ->editColumn('amount', function ($row) {
                        return 'Rs. ' . number_format($row->amount, 2);
                    })
                    ->editColumn('date', function ($row) {
                        return isset($row->date)
                            ? date('d-M-Y', strtotime($row->date))
                            : '';
                    })
                    ->editColumn('comment', function ($row) {
                        return $row->comment ?? '—';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl   = route('admin.overheads.edit', $row->id);
                        $showUrl   = route('admin.overheads.show', $row->id);
                        $deleteUrl = route('admin.overheads.destroy', $row->id);
                        $csrf      = csrf_token();

                        return '
                            <a href="' . $showUrl . '" class="btn btn-sm btn-info">View</a>
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                            <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;"
                                  onsubmit="return confirm(\'Are you sure?\')">
                                <input type="hidden" name="_token" value="' . $csrf . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        } catch (\Exception $ex) {
            return redirect('admin/overheads')->with('error', $ex->getMessage());
        }

        return view('admin.overheads.index');
    }

    public function create()
    {
        $expenseTypes = ExpenseType::orderBy('name')->get();
        // late status drivers — welfare k liye
        $lateDrivers  = Driver::where('status', 'late')->orderBy('name')->get();

        return view('admin.overheads.create', compact('expenseTypes', 'lateDrivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'driver_id'       => 'nullable|exists:drivers,id',
            'amount'          => 'required|numeric|min:0',
            'date'            => 'required|date',
            'comment'         => 'nullable|string|max:1000',
        ]);

        // Agar welfare nahi hai to driver_id null rakho
        $expenseType = ExpenseType::findOrFail($validated['expense_type_id']);
        if (strtolower($expenseType->name) !== 'welfare') {
            $validated['driver_id'] = null;
        }
        Overhead::create($validated);
        return redirect()->route('admin.overheads.index')->with('success', 'Overhead expense added successfully!');
    }

    public function show(Overhead $overhead)
    {
        $overhead->load(['expenseType', 'driver']);
        return view('admin.overheads.show', compact('overhead'));
    }

    public function edit(Overhead $overhead)
    {
        $expenseTypes = ExpenseType::orderBy('name')->get();
        $lateDrivers  = Driver::where('status', 'late')->orderBy('name')->get();

        return view('admin.overheads.edit', compact('overhead', 'expenseTypes', 'lateDrivers'));
    }

    public function update(Request $request, Overhead $overhead)
    {
        $validated = $request->validate([
            'expense_type_id' => 'required|exists:expense_types,id',
            'driver_id'       => 'nullable|exists:drivers,id',
            'amount'          => 'required|numeric|min:0',
            'date'            => 'required|date',
            'comment'         => 'nullable|string|max:1000',
        ]);

        $expenseType = ExpenseType::findOrFail($validated['expense_type_id']);
        if (strtolower($expenseType->name) !== 'welfare') {
            $validated['driver_id'] = null;
        }

        $overhead->update($validated);

        return redirect()->route('admin.overheads.index')
                         ->with('success', 'Overhead updated successfully!');
    }

    public function destroy(Overhead $overhead)
    {
        $overhead->delete();

        return redirect()->route('admin.overheads.index')
                         ->with('success', 'Overhead deleted successfully!');
    }
}
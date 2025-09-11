<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseType;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenseTypes = ExpenseType::latest()->paginate(10);
        return view('admin.expense_types.index', compact('expenseTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.expense_types.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_types,name',
        ]);

        ExpenseType::create($request->only('name'));

        return redirect()->route('admin.expense-types.index')->with('success', 'Expense type created successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit(ExpenseType $expenseType)
    {
        return view('admin.expense_types.edit', compact('expenseType'));
    }

    public function update(Request $request, ExpenseType $expenseType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_types,name,' . $expenseType->id,
        ]);

        $expenseType->update($request->only('name'));

        return redirect()->route('admin.expense-types.index')
                         ->with('success', 'Expense type updated successfully.');
    }

    public function destroy(ExpenseType $expenseType)
    {
         $expenseType->delete();

        return redirect()->route('admin.expense-types.index')
                         ->with('success', 'Expense type deleted successfully.');
    }
}

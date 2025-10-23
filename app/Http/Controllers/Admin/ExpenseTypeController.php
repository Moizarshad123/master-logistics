<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseType;
use App\Models\ExpenseCategory;

class ExpenseTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenseTypes = ExpenseType::orderByDESC('id')->paginate(10);
        return view('admin.expense_types.index', compact('expenseTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = ExpenseCategory::all();

        return view('admin.expense_types.create', compact("categories"));
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

        ExpenseType::create([
            'name' => $request->name,
            "category_id" => $request->category_id
        ]);

        return redirect()->route('admin.expense-types.index')->with('success', 'Expense type created successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit(ExpenseType $expenseType)
    {
        $categories = ExpenseCategory::all();
        return view('admin.expense_types.edit', compact('expenseType', 'categories'));
    }

    public function update(Request $request, ExpenseType $expenseType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:expense_types,name,' . $expenseType->id,
        ]);

        $expenseType->update($request->only('name', 'category_id'));

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

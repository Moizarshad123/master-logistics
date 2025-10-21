<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseFrom;

class ExpenseFromController extends Controller
{
    public function index()
    {
        $expenseForms = ExpenseFrom::latest()->get();
        return view('admin.expense_from.index', compact('expenseForms'));
    }

    public function create()
    {
        return view('admin.expense_from.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ExpenseFrom::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.expense-from.index')
                         ->with('success', 'Expense From created successfully.');
    }

    public function edit(ExpenseFrom $expenseFrom)
    {
        return view('admin.expense_from.edit', compact('expenseFrom'));
    }

    public function update(Request $request, ExpenseFrom $expenseFrom)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $expenseFrom->update([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.expense-from.index')
                         ->with('success', 'Expense From updated successfully.');
    }

    public function destroy(ExpenseFrom $expenseFrom)
    {
        $expenseFrom->delete();

        return redirect()->route('admin.expense-from.index')
                         ->with('success', 'Expense From deleted successfully.');
    }
}

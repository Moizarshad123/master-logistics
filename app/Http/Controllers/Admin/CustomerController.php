<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerHead;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with("customerHead")->orderByDESC("id")->paginate(25);
        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        $customerHeads = CustomerHead::all();
        return view('admin.customers.create', compact("customerHeads"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name.*'             => 'required|string|max:255',
            'customer_head_id.*' => 'required|integer|exists:customer_heads,id',
        ]);

        // Loop through all customer entries
        foreach ($request->name as $index => $name) {
            Customer::create([
                'name'             => $name,
                'customer_head_id' => $request->customer_head_id[$index],
            ]);
        }
        return redirect()->route('admin.customers.index')->with('success', 'Customer added successfully.');
    }

    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $customerHeads = CustomerHead::all();
        return view('admin.customers.edit', compact('customerHeads', 'customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'customer_head_id' => 'required|exists:customer_heads,id',
        ]);

        $customer->update($request->only('customer_head_id', 'name'));
        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerHead;

class CustomerHeadController extends Controller
{
    public function index()
    {
        $customerHeads = CustomerHead::all();
        return view('admin.customer_heads.index', compact('customerHeads'));
    }

    public function create()
    {
        return view('admin.customer_heads.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        CustomerHead::create($request->only('customer_head_id', 'name'));
        return redirect()->route('admin.customer-heads.index')->with('success', 'Customer Head added successfully.');
    }

    
    public function show(CustomerHead $customerHead)
    {
        return view('admin.customer_heads.show', compact('customerHead'));
    }

    public function edit(CustomerHead $customerHead)
    {
        return view('admin.customer_heads.edit', compact('customerHead'));
    }

    public function update(Request $request, CustomerHead $customerHead)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $customerHead->update($request->only('name'));
        return redirect()->route('admin.customer-heads.index')->with('success', 'Customer Head updated successfully.');
    }

    public function destroy(CustomerHead $customerHead)
    {
        $customerHead->delete();
        return redirect()->route('admin.customer-heads.index')->with('success', 'Customer Head deleted successfully.');
    }
}

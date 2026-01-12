<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\AmountReceivable;
use App\Models\CustomerHead;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            if (request()->ajax()) {
                $customers = Customer::with("customerHead");
                return datatables()->eloquent($customers->orderByDesc('id'))
                    ->addColumn('customerHead', function ($data) {
                        return $data->customerHead->name ?? '';
                    })
                    ->editColumn('outstanding_amount', function ($data) {
                        return $data->outstanding_amount ?? '';
                    })
                    ->addColumn('action', function ($data) {

                        $viewUrl    = route('admin.customers.show', $data->id);
                        $editUrl    = route('admin.customers.edit', $data->id);
                        $deleteUrl  = route('admin.customers.destroy', $data->id);

                        return '
                        <a href="'.$viewUrl.'" class="btn btn-sm btn-info">View Payment History</a> |
                        <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> |
                            <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                    })
                    ->rawColumns(['action', 'customerHead'])->make(true);

            }
        } catch (\Exception $ex) {
            return redirect('admin/customers')->with('error', $ex->getMessage());
        }

        return view('admin.customers.index');
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
        $payments = AmountReceivable::where("customer_id", $customer->id)->orderByDESC("id")->get();
        return view('admin.customers.show', compact('payments'));
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

        $customer->update($request->only('customer_head_id', 'name', 'outstanding_amount'));
        return redirect()->route('admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('admin.customers.index')->with('success', 'Customer deleted successfully.');
    }
}

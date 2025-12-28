<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AmountReceivable;
use App\Models\Customer;
use App\Models\Trip;
use DB;

class AmountReceivableController extends Controller
{
    public function index()
    {
        $receivables = AmountReceivable::with('customer', 'trip')->latest()->get();
        return view('admin.amount_receivables.index', compact('receivables'));
    }

    public function create()
    {
        return view('admin.amount_receivables.create', [
            'customers' => Customer::all(),
            'trips'     => Trip::all()
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validate([
                'trip_id'      => 'nullable',
                'customer_id'  => 'required',
                'payment_type' => 'required',
                'other_source' => 'nullable',
                'date'         => 'nullable',
                'amount'       => 'required|numeric',
            ]);
    
            $data["receipt"] = "";
            $dir             = "uploads/drivers/";
            if ($request->hasFile('receipt')) {
                $file     = $request->file('receipt');
                $fileName = time() . '-' . uniqid() . '-receipt.' . $file->getClientOriginalExtension();
                $file->move($dir, $fileName);
                $data["receipt"] = $dir.$fileName;
    
            }
    
            $amount = AmountReceivable::create($data);

            $customer  = Customer::findOrFail($request->customer_id);
            $customer->outstanding_amount -= $request->amount;
            $customer->save();

            DB::commit();
    
            return redirect()->route('admin.amount-receivables.index')
                ->with('success', 'Amount Receivable Added');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function edit(AmountReceivable $amountReceivable)
    {
        return view('admin.amount_receivables.edit', [
            'receivable' => $amountReceivable,
            'customers' => Customer::all(),
            'trips'     => Trip::all()
        ]);
    }

    public function update(Request $request, AmountReceivable $amountReceivable)
    {
        $data = $request->validate([
            'trip_id'      => 'nullable',
            'customer_id'  => 'required',
            'payment_type' => 'required',
            'other_source' => 'nullable',
            'date'         => 'nullable',
            'amount'       => 'required|numeric',
            'receipt'      => 'nullable|image'
        ]);

        $dir             = "uploads/drivers/";
        if ($request->hasFile('receipt')) {
            $file     = $request->file('receipt');
            $fileName = time() . '-' . uniqid() . '-receipt.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $data["receipt"] = $dir.$fileName;

        }

        $amountReceivable->update($data);

        return redirect()->route('admin.amount-receivables.index')->with('success', 'Updated Successfully');
    }

    public function destroy(AmountReceivable $amountReceivable)
    {
        $amountReceivable->delete();
        return back()->with('success', 'Deleted Successfully');
    }
}


<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmountPayable;
use App\Models\FuelSupplier;
use Illuminate\Http\Request;
use DB;

class AmountPayableController extends Controller
{
    public function index()
    {
        $records = AmountPayable::with('supplier')->latest()->get();
        return view('admin.amount-payables.index', compact('records'));
    }

    public function create()
    {
        $suppliers = FuelSupplier::all();
        return view('admin.amount-payables.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validate([
                'supplier_id'  => 'required',
                'amount'       => 'required|numeric',
                'date'         => 'nullable|date',
                'payment_via'  => 'required',
                'other_source' => 'nullable',
                'receipt'      => 'nullable|image'
            ]);
    
            if ($request->hasFile('receipt')) {
                $data['receipt'] = $this->uploadImage($request, 'receipt');
            }
    
            AmountPayable::create($data);
            $supplier                     = FuelSupplier::findOrFail($data["supplier_id"]);
            $supplier->outstanding_amount -= $data["amount"];
            $supplier->save();
    
            DB::commit();

            return redirect()->route('admin.amount-payables.index')
                ->with('success', 'Amount Payable Added Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('success', $e->getMessage());
        }
    }

    public function edit(AmountPayable $amountPayable)
    {
        $suppliers = FuelSupplier::all();
        return view('admin.amount-payables.edit', compact('amountPayable', 'suppliers'));
    }

    public function update(Request $request, AmountPayable $amountPayable)
    {
        $data = $request->validate([
            'supplier_id'  => 'required',
            'amount'       => 'required|numeric',
            'date'         => 'nullable|date',
            'payment_via'  => 'required',
            'other_source' => 'nullable',
            'receipt'      => 'nullable|image'
        ]);

        if ($request->hasFile('receipt')) {
            $data['receipt'] = $this->uploadImage($request, 'receipt');
        }

        $amountPayable->update($data);

        return redirect()->route('admin.amount-payables.index')
            ->with('success', 'Amount Payable Updated Successfully');
    }

    public function destroy(AmountPayable $amountPayable)
    {
        $amountPayable->delete();
        return back()->with('success', 'Record Deleted');
    }

    private function uploadImage(Request $request, $field)
    {
        $file = $request->file($field);
        $name = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'uploads/amount-payables/';
        $file->move($path, $name);

        return asset($path . $name);
    }
}

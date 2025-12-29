<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MasterSweetner;
use App\Models\FuelSupplier;
use App\Models\Setting;
use Illuminate\Http\Request;
use DB;

class MasterSweetnerController extends Controller
{
    public function index()
    {
        $sweetners = MasterSweetner::with('supplier')->latest()->get();
        return view('admin.master-sweetners.index', compact('sweetners'));
    }

    public function create()
    {
        $suppliers = FuelSupplier::all();
        return view('admin.master-sweetners.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        try {

            DB::beginTransaction();
            $data = $request->validate([
                'supplier_id'        => 'required',
                'total_litres'       => 'required|numeric',
                'per_litre_price'    => 'required|numeric',
                'total_amount'       => 'required|numeric',
                'date'               => 'nullable|date',
                'fuel_type'          => 'nullable',
                'receiving_receipt'  => 'nullable|image',
                'delivery_challan'   => 'nullable|image',
            ]);
    
            $data['receiving_receipt'] = $this->uploadImage($request, 'receiving_receipt');
            $data['delivery_challan']  = $this->uploadImage($request, 'delivery_challan');
    
            MasterSweetner::create($data);
    
            $supplier                     = FuelSupplier::findOrFail($data["supplier_id"]);
            $supplier->outstanding_amount += $data["total_amount"];
            $supplier->save();

            $setting = Setting::findOrFail(1);
            if($request->fuel_type == "Petrol") {
                $setting->total_petrol += $data["total_litres"];
            } elseif($request->fuel_type == "Diesel") {
                $setting->total_diesel += $data["total_litres"];
            }   
            $setting->save();

            DB::commit();
    
            return redirect()->route('admin.master-sweetners.index')->with('success', 'Master Sweetner Added Successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('success', $e->getMessage());
        }
    }

    public function edit(MasterSweetner $masterSweetner)
    {
        $suppliers = FuelSupplier::all();
        return view('admin.master-sweetners.edit', compact('masterSweetner', 'suppliers'));
    }

    public function update(Request $request, MasterSweetner $masterSweetner)
    {
        $data = $request->validate([
            'supplier_id'        => 'required',
            'total_litres'       => 'required|numeric',
            'per_litre_price'    => 'required|numeric',
            'total_amount'       => 'required|numeric',
            'date'               => 'nullable|date',
            'fuel_type'          => 'nullable',
            'receiving_receipt'  => 'nullable|image',
            'delivery_challan'   => 'nullable|image',
        ]);

        if ($request->hasFile('receiving_receipt')) {
            $data['receiving_receipt'] = $this->uploadImage($request, 'receiving_receipt');
        }

        if ($request->hasFile('delivery_challan')) {
            $data['delivery_challan'] = $this->uploadImage($request, 'delivery_challan');
        }

        $masterSweetner->update($data);

        return redirect()->route('admin.master-sweetners.index')
            ->with('success', 'Master Sweetner Updated Successfully');
    }

    public function destroy(MasterSweetner $masterSweetner)
    {
        $masterSweetner->delete();
        return back()->with('success', 'Record Deleted');
    }

    private function uploadImage(Request $request, $field)
    {
        if (!$request->hasFile($field)) {
            return null;
        }

        $file = $request->file($field);
        $name = time() . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'uploads/master-sweetners/';
        $file->move($path, $name);

        return asset($path . $name);
    }
}

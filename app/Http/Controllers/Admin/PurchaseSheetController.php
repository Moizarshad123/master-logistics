<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseSheet;

class PurchaseSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function show_sheet($id)
    {
        $purchase = PurchaseSheet::find($id);

        if (!$purchase) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'id'           => $purchase->id,
            'station'      => $purchase->station,
            'per_ton_rate' => $purchase->per_ton_rate,
            'type'         => $purchase->type,
        ]);
    }

    public function index()
    {
        $purchases = PurchaseSheet::orderByDESC("id")->paginate(50);
        return view("admin.purchases.index", compact("purchases"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("admin.purchases.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $purchase = PurchaseSheet::create([
            "station"      => $request->station,
            "per_ton_rate" => $request->per_ton_rate,
            "type"         => $request->type,
        ]);
        return redirect()->route('admin.purchases.index')->with('success', 'New Purchase added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $salesSheet = PurchaseSheet::findOrFail($id);
        return view('admin.purchases.edit', compact('salesSheet'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $SaleSheet              = PurchaseSheet::find($id);
        $SaleSheet->station      = $request->station;
        $SaleSheet->per_ton_rate = $request->per_ton_rate;
        $SaleSheet->type = $request->type;
        $SaleSheet->save();

        return redirect()->route('admin.purchases.index')->with('success', 'Purchase updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sheet = PurchaseSheet::find($id);
        $sheet->delete();
        return redirect()->route('admin.purchases.index')->with('success', 'Purchase deleted!');
    }
}

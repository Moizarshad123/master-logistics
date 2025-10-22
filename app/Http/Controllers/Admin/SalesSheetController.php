<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleSheet;

class SalesSheetController extends Controller
{
    public function index()
    {
        $sales = SaleSheet::orderByDESC("id")->paginate(50);
        return view("admin.sales.index", compact("sales"));
    }

    public function show_sheet($id)
    {
        $sale = SaleSheet::find($id);

        if (!$sale) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'id'            => $sale->id,
            'station'       => $sale->station,
            'minimum_rent' => $sale->minimum_rent,
            'per_bag_rate' => $sale->per_bag_rate,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $salesSheet = SaleSheet::findOrFail($id);
        return view('admin.sales.edit', compact('salesSheet'));
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

        $SaleSheet               = SaleSheet::find($id);
        $SaleSheet->station      = $request->station;
        $SaleSheet->minimum_rent = $request->minimum_rent;
        $SaleSheet->per_bag_rate = $request->per_bag_rate;
        $SaleSheet->save();

        return redirect()->route('admin.sales.index')->with('success', 'Sales updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sheet = SaleSheet::find($id);
        $sheet->delete();
        return redirect()->route('admin.sales.index')->with('success', 'Sales deleted!');


    }
}

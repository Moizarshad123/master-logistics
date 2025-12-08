<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PurchaseSheet;

class PurchaseSheetController extends Controller
{
    public function show_sheet($id)
    {
        $purchase = PurchaseSheet::where('station', 'LIKE', '%' . $id . '%')->first();

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

        try {
            if (request()->ajax()) {
                $purchases = PurchaseSheet::orderByDESC("id");

                return datatables()->eloquent($purchases)
                    ->addColumn('action', function ($data) {

                        $viewUrl    = route('admin.purchases.show', $data->id);
                        $editUrl    = route('admin.purchases.edit', $data->id);
                        $deleteUrl  = route('admin.purchases.destroy', $data->id);

                        return '
                            <a href="'.$viewUrl.'" class="btn btn-sm btn-info">View</a> |
                            <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> |
                            <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                    })
                    ->rawColumns(['action'])->make(true);

            }
        } catch (\Exception $ex) {
            return redirect('admin/purchases')->with('error', $ex->getMessage());
        }

        return view("admin.purchases.index");
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

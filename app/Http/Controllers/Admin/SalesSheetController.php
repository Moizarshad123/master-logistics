<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SaleSheet;

class SalesSheetController extends Controller
{
    public function index()
    {
        try {
            if (request()->ajax()) {
                $sales = SaleSheet::orderByDESC("id");

                return datatables()->eloquent($sales)
                    ->addColumn('action', function ($data) {

                        $viewUrl    = route('admin.sales.show', $data->id);
                        $editUrl    = route('admin.sales.edit', $data->id);
                        $deleteUrl  = route('admin.sales.destroy', $data->id);

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
            return redirect('admin/sales')->with('error', $ex->getMessage());
        }

        return view("admin.sales.index");
    }

    public function show_sheet($id)
    {
         $sale = SaleSheet::where('station', 'LIKE', '%' . $id . '%')->first();

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
        return view("admin.sales.create");
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $SaleSheet = SaleSheet::create([
            "station"      => $request->station,
            "minimum_rent" => $request->minimum_rent,
            "per_bag_rate" => $request->per_bag_rate,
        ]);
        return redirect()->route('admin.sales.index')->with('success', 'Sales added!');
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

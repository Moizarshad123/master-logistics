<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\InventoryItem;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource (DataTables AJAX + HTML).
     */
    public function index()
    {
        try {
            if (request()->ajax()) {
                $inventories = Inventory::with("item")->latest();

                return datatables()->eloquent($inventories)
                    ->addIndexColumn()
                    ->addColumn('item_name', function ($row) {
                        return $row->item?->name;
                    })
                    
                    ->editColumn('price', function ($row) {
                        return 'Rs. ' . number_format($row->price, 2);
                    })
                    ->addColumn('unit_price', function ($row) {
                        return 'Rs. ' . number_format($row->item?->price, 2);
                    })
                    ->editColumn('purchase_date', function ($row) {
                        return isset($row->purchase_date)
                            ? date('d-M-Y', strtotime($row->purchase_date))
                            : '';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl   = route('admin.inventories.edit', $row->id);
                        $showUrl   = route('admin.inventories.show', $row->id);
                        $deleteUrl = route('admin.inventories.destroy', $row->id);
                        $csrf      = csrf_token();

                        return '
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                            <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;"
                                  onsubmit="return confirm(\'Are you sure you want to delete this item?\')">
                                <input type="hidden" name="_token" value="' . $csrf . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>';
                    })
                    ->rawColumns(['action', 'item_name', 'unit_price'])
                    ->make(true);
            }
        } catch (\Exception $ex) {
            return redirect('admin/inventories')->with('error', $ex->getMessage());
        }

        return view('admin.inventories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $items = InventoryItem::orderBy("name", 'ASC')->get();
        return view('admin.inventories.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_id'       => 'required|int',
            'price'         => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'qty'           => 'required|integer|min:0',
        ]);
        $validated['remaining_qty'] = $validated['qty'];
        Inventory::create($validated);

        return redirect()->route('admin.inventories.index')
                         ->with('success', 'Item added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Inventory $inventory)
    {
        return view('admin.inventories.show', compact('inventory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        return view('admin.inventories.edit', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'item_id'       => 'required|int',
            'price'         => 'required|numeric|min:0',
            'purchase_date' => 'required|date',
            'qty'           => 'required|integer|min:0',
        ]);

        $inventory->update($validated);

        return redirect()->route('admin.inventories.index')
                         ->with('success', 'Purchase item updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return redirect()->route('admin.inventories.index')
                         ->with('success', 'Item deleted successfully!');
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItem;

class InventoryItemController extends Controller
{
    public function index()
    {
        try {
            if (request()->ajax()) {
                $inventoryItems = InventoryItem::latest();

                return datatables()->eloquent($inventoryItems)
                    ->addIndexColumn()
                    ->addColumn('action', function ($row) {
                        $editUrl   = route('admin.inventory-items.edit', $row->id);
                        $deleteUrl = route('admin.inventory-items.destroy', $row->id);
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
                    ->rawColumns(['action'])
                    ->make(true);
            }
        } catch (\Exception $ex) {
            return redirect('admin/inventory-items')->with('error', $ex->getMessage());
        }

        return view('admin.inventory_items.index');
    }

    public function create()
    {
        return view('admin.inventory_items.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'make'  => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'unit'  => 'nullable|string|max:100',
        ]);

        InventoryItem::create($request->only('name', 'make', 'model', 'unit'));

        return redirect()->route('admin.inventory-items.index')
                         ->with('success', 'Inventory Item created successfully.');
    }

    public function edit(InventoryItem $inventoryItem)
    {
        return view('admin.inventory_items.edit', compact('inventoryItem'));
    }

    public function update(Request $request, InventoryItem $inventoryItem)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'make'  => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'unit'  => 'nullable|string|max:100',
        ]);

        $inventoryItem->update($request->only('name', 'make', 'model', 'unit'));

        return redirect()->route('admin.inventory-items.index')
                         ->with('success', 'Inventory Item updated successfully.');
    }

    public function destroy(InventoryItem $inventoryItem)
    {
        $inventoryItem->delete();

        return redirect()->route('admin.inventory-items.index')
                         ->with('success', 'Inventory Item deleted successfully.');
    }
}
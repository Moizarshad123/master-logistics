<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Issuance;
use App\Models\Inventory;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class IssuanceController extends Controller
{
    /**
     * Display listing with DataTables.
     */
    public function index()
    {
        try {
            if (request()->ajax()) {
                $issuances = Issuance::with(['vehicle', 'inventory'])->latest();

                return datatables()->eloquent($issuances)
                    ->addIndexColumn()
                    ->editColumn('issue_date', function ($row) {
                        return isset($row->issue_date)
                            ? date('d-M-Y', strtotime($row->issue_date))
                            : '';
                    })
                    ->addColumn('vehicle_no', function ($row) {
                        return $row->vehicle->vehicle_no ?? '—';
                    })
                    ->addColumn('item_name', function ($row) {
                        return $row->inventory->item_name ?? '—';
                    })
                    ->addColumn('remaining_qty', function ($row) {
                        return $row->inventory->remaining_qty ?? '—';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl   = route('admin.issuances.edit', $row->id);
                        $showUrl   = route('admin.issuances.show', $row->id);
                        $deleteUrl = route('admin.issuances.destroy', $row->id);
                        $csrf      = csrf_token();

                        return '
                            <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                            <form action="' . $deleteUrl . '" method="POST" style="display:inline-block;"
                                  onsubmit="return confirm(\'Are you sure? Qty will be restored to inventory.\')">
                                <input type="hidden" name="_token" value="' . $csrf . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        } catch (\Exception $ex) {
            return redirect('admin/issuances')->with('error', $ex->getMessage());
        }

        return view('admin.issuances.index');
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $vehicles   = Vehicle::select('id', 'vehicle_no')->orderBy('vehicle_no')->get();

        // item_name groupBy — har unique item_name ka ek entry, remaining_qty > 0
        $inventories = Inventory::where('remaining_qty', '>', 0)
                                ->select('id', 'item_name', 'remaining_qty')
                                ->orderBy('item_name')
                                ->get()
                                ->groupBy('item_name');

        return view('admin.issuances.create', compact('vehicles', 'inventories'));
    }

    /**
     * Store new issuance — qty check + inventory deduction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id'   => 'required|exists:vehicles,id',
            'inventory_id' => 'required|exists:inventories,id',
            'qty'          => 'required|integer|min:1',
            'issue_date'   => 'required|date',
        ]);

        $inventory = Inventory::findOrFail($request->inventory_id);

        // Qty check
        if ($request->qty > $inventory->remaining_qty) {
            return back()
                ->withInput()
                ->withErrors(['qty' => "Insufficient stock! Available qty: {$inventory->remaining_qty}"]);
        }

        // Issuance create
        Issuance::create($request->only('vehicle_id', 'inventory_id', 'qty', 'issue_date'));

        // Inventory remaining_qty reduce
        $inventory->decrement('remaining_qty', $request->qty);

        return redirect()->route('admin.issuances.index')
                         ->with('success', 'Issuance created successfully!');
    }

    /**
     * Show detail.
     */
    public function show(Issuance $issuance)
    {
        $issuance->load(['vehicle', 'inventory']);
        return view('admin.issuances.show', compact('issuance'));
    }

    /**
     * Show edit form.
     */
    public function edit(Issuance $issuance)
    {
        $vehicles    = Vehicle::select('id', 'vehicle_no')->orderBy('vehicle_no')->get();
        $inventories = Inventory::select('id', 'item_name', 'remaining_qty')
                                ->orderBy('item_name')
                                ->get()
                                ->groupBy('item_name');

        return view('admin.issuances.edit', compact('issuance', 'vehicles', 'inventories'));
    }

    /**
     * Update issuance — restore old qty, then deduct new qty.
     */
    public function update(Request $request, Issuance $issuance)
    {
        $request->validate([
            'vehicle_id'   => 'required|exists:vehicles,id',
            'inventory_id' => 'required|exists:inventories,id',
            'qty'          => 'required|integer|min:1',
            'issue_date'   => 'required|date',
        ]);

        $newInventory = Inventory::findOrFail($request->inventory_id);
        $oldInventory = Inventory::findOrFail($issuance->inventory_id);

        // Agar inventory change hua ya nahi — available qty calculate
        if ($newInventory->id === $oldInventory->id) {
            // Same item — old qty wapas add kar ke check
            $availableQty = $newInventory->remaining_qty + $issuance->qty;
        } else {
            // Different item — old inventory restore, new inventory check
            $availableQty = $newInventory->remaining_qty;
        }

        if ($request->qty > $availableQty) {
            return back()
                ->withInput()
                ->withErrors(['qty' => "Insufficient stock! Available qty: {$availableQty}"]);
        }

        // Restore old inventory qty
        $oldInventory->increment('remaining_qty', $issuance->qty);

        // Update issuance
        $issuance->update($request->only('vehicle_id', 'inventory_id', 'qty', 'issue_date'));

        // Deduct new inventory qty
        $newInventory->decrement('remaining_qty', $request->qty);

        return redirect()->route('admin.issuances.index')
                         ->with('success', 'Issuance updated successfully!');
    }

    /**
     * Delete issuance — restore qty to inventory.
     */
    public function destroy(Issuance $issuance)
    {
        // Qty restore
        $issuance->inventory->increment('remaining_qty', $issuance->qty);

        $issuance->delete();

        return redirect()->route('admin.issuances.index')
                         ->with('success', 'Issuance deleted and qty restored to inventory!');
    }

    /**
     * AJAX — get inventory info by ID (for qty check in form).
     */
    public function getInventoryQty($inventoryId)
    {
        $inventory = Inventory::findOrFail($inventoryId);
        return response()->json([
            'remaining_qty' => $inventory->remaining_qty,
            'item_name'     => $inventory->item_name,
        ]);
    }
}
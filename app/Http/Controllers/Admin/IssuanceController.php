<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Issuance;
use App\Models\Inventory;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class IssuanceController extends Controller
{

    public function index()
    {
        try {
            if (request()->ajax()) {
                $issuances = Issuance::with(['vehicle', 'inventory.item'])->latest();

                return datatables()->eloquent($issuances)
                    ->addIndexColumn()
                    ->editColumn('issue_date', function ($row) {
                        return $row->issue_date
                            ? $row->issue_date->format('d-M-Y')
                            : '';
                    })
                    ->addColumn('vehicle_no', function ($row) {
                        return $row->vehicle->vehicle_no ?? '—';
                    })
                    ->addColumn('item_name', function ($row) {
                        return $row->inventory?->item?->name ?? '—';
                    })
                    ->addColumn('remaining_qty', function ($row) {
                        return $row->inventory->remaining_qty ?? '—';
                    })
                    ->addColumn('action', function ($row) {
                        $editUrl   = route('admin.issuances.edit', $row->id);
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

    public function create()
    {
        $inventories = Inventory::with('item')
                        ->where('remaining_qty', '>', 0)
                        ->orderBy('id', 'ASC')
                        ->get();

        $vehicles = Vehicle::select('id', 'vehicle_no')->orderBy('vehicle_no')->get();

        return view('admin.issuances.create', compact('vehicles', 'inventories'));
    }

    /**
     * Store new issuance — qty check + inventory deduction.
     */
    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'item_id'    => 'required|exists:inventories,id',
            'qty'        => 'required|integer|min:1',
            'issue_date' => 'required|date',
            'remarks'    => 'nullable|string|max:500',
        ]);

        $inventory = Inventory::findOrFail($request->item_id);

        // Qty check
        if ($request->qty > $inventory->remaining_qty) {
            return back()
                ->withInput()
                ->withErrors(['qty' => "Insufficient stock! Available qty: {$inventory->remaining_qty}"]);
        }

        // Issuance create
        Issuance::create($request->only('vehicle_id', 'item_id', 'qty', 'issue_date', 'remarks'));

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
        $issuance->load(['vehicle', 'inventory.item']);
        return view('admin.issuances.show', compact('issuance'));
    }

    /**
     * Show edit form.
     */
    public function edit(Issuance $issuance)
    {
        $vehicles = Vehicle::select('id', 'vehicle_no')->orderBy('vehicle_no')->get();

        // Include current issuance's inventory even if remaining_qty is 0
        $inventories = Inventory::with('item')
                        ->where(function ($q) use ($issuance) {
                            $q->where('remaining_qty', '>', 0)
                              ->orWhere('id', $issuance->item_id);
                        })
                        ->orderBy('id', 'ASC')
                        ->get();

        return view('admin.issuances.edit', compact('issuance', 'vehicles', 'inventories'));
    }

    /**
     * Update issuance — restore old qty, then deduct new qty.
     */
    public function update(Request $request, Issuance $issuance)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'item_id'    => 'required|exists:inventories,id',
            'qty'        => 'required|integer|min:1',
            'issue_date' => 'required|date',
            'remarks'    => 'nullable|string|max:500',
        ]);

        $newInventory = Inventory::findOrFail($request->item_id);
        $oldInventory = Inventory::findOrFail($issuance->item_id);

        // Available qty calculate
        if ($newInventory->id === $oldInventory->id) {
            // Same item — old qty wapas add kar ke check
            $availableQty = $newInventory->remaining_qty + $issuance->qty;
        } else {
            // Different item — new inventory ki remaining check
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
        $issuance->update($request->only('vehicle_id', 'item_id', 'qty', 'issue_date', 'remarks'));

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
        $issuance->inventory->increment('remaining_qty', $issuance->qty);
        $issuance->delete();

        return redirect()->route('admin.issuances.index')
                         ->with('success', 'Issuance deleted and qty restored to inventory!');
    }
}
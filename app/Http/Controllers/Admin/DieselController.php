<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Diesel;
use Auth;
class DieselController extends Controller
{
    public function index()
    {
        try {
            if (request()->ajax()) {
                $diesels = Diesel::with("user");
                return datatables()->eloquent($diesels->orderByDesc('id'))
                    ->addColumn('createdBy', function ($data) {
                        if($data->created_by != null) {
                            return $data->user->name;
                        } else {
                            return "";
                        }
                    })
                    ->addColumn('dateTime', function ($data) {
                        return  date('d M Y', strtotime($data->date)).' - '.date('H:i A', strtotime($data->time));
                    })   
                    ->addColumn('action', function ($data) {

                        $editUrl    = route('admin.diesel.edit', $data->id);
                        $deleteUrl  = route('admin.diesel.destroy', $data->id);

                        return '
                            <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> |
                            <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                    })
                    ->rawColumns(['action', 'createdBy', 'dateTime'])->make(true);

            }
        } catch (\Exception $ex) {
            return redirect('admin/diesel')->with('error', $ex->getMessage());
        }

        
        return view('admin.diesel.index');
    }

    public function create()
    {
        return view('admin.diesel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'             => 'required',
            'date'             => 'required|date',
            'time'             => 'required',
            'litres'           => 'required|numeric',
            'per_litre_amount' => 'required|numeric',
            'total_amount'     => 'required|numeric',
        ]);

        Diesel::create([
            'type'             => $request->type,
            'date'             => $request->date,
            'time'             => $request->time,
            'litres'           => $request->litres,
            'per_litre_amount' => $request->per_litre_amount,
            'total_amount'     => $request->total_amount,
            'created_by'       => Auth::id(),
        ]);

        return redirect()->route('admin.diesel.index')->with('success', 'Record created successfully.');
    }

    public function show($id)
    {
        $diesel = Diesel::findOrFail($id);
        return view('admin.diesel.show', compact('diesel'));
    }

    public function edit($id)
    {
        $diesel = Diesel::findOrFail($id);
        return view('admin.diesel.edit', compact('diesel'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'type'   => 'required',
            'date'   => 'required|date',
            'time'   => 'required',
            'litres' => 'required|numeric',
            'per_litre_amount' => 'required|numeric',
            'total_amount' => 'required|numeric',
        ]);

        $diesel = Diesel::findOrFail($id);
        $diesel->update($request->all());

        return redirect()->route('admin.diesel.index')->with('success', 'Record updated successfully.');
    }

    public function destroy($id)
    {
        Diesel::findOrFail($id)->delete();
        return back()->with('success', 'Record deleted successfully.');
    }
}

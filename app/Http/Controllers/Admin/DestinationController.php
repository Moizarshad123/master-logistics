<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Destination;

class DestinationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            if (request()->ajax()) {
                $destinations = Destination::orderByDESC('id');

                return datatables()->eloquent($destinations)
                    ->addColumn('action', function ($data) {

                        $viewUrl    = route('admin.destinations.show', $data->id);
                        $editUrl    = route('admin.destinations.edit', $data->id);
                        $deleteUrl  = route('admin.destinations.destroy', $data->id);

                        return '<a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> |
                            <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                    })
                    ->rawColumns(['action'])->make(true);

            }
        } catch (\Exception $ex) {
            return redirect('admin/destinations')->with('error', $ex->getMessage());
        }

        return view('admin.destinations.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.destinations.create');
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name.*' => 'required|string|max:255',
        ]);
        foreach ($request->name as $name) {
            Destination::create(['name' => $name]);
        }
        return redirect()->route('admin.destinations.index')->with('success', 'Destination added!');
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

    public function edit(Destination $destination)
    {
        return view('admin.destinations.edit', compact('destination'));
    }

    public function update(Request $request, Destination $destination)
    {
        $request->validate(['name' => 'required']);
        $destination->update($request->all());
        return redirect()->route('admin.destinations.index')->with('success', 'Destination updated!');
    }

    public function destroy(Destination $destination)
    {
        $destination->delete();
        return redirect()->route('admin.destinations.index')->with('success', 'Destination deleted!');
    }
}

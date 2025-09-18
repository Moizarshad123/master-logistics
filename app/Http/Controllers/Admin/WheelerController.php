<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wheeler;

class WheelerController extends Controller
{
    public function index()
    {
        $wheelers = Wheeler::orderByDESC('id')->get();
        return view('admin.wheelers.index', compact('wheelers'));
    }

    public function create()
    {
        return view('admin.wheelers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:wheelers,name|max:255',
        ]);

        Wheeler::create($request->only('name'));

        return redirect()->route('admin.wheelers.index')->with('success', 'Wheeler created successfully.');
    }

    public function edit(Wheeler $wheeler)
    {
        return view('admin.wheelers.edit', compact('wheeler'));
    }

    public function update(Request $request, Wheeler $wheeler)
    {
        $request->validate([
            'name' => 'required|unique:wheelers,name,' . $wheeler->id,
        ]);

        $wheeler->update($request->only('name'));

        return redirect()->route('admin.wheelers.index')->with('success', 'Wheeler updated successfully.');
    }

    public function destroy(Wheeler $wheeler)
    {
        $wheeler->delete();

        return redirect()->route('admin.wheelers.index')->with('success', 'Wheeler deleted successfully.');
    }

}

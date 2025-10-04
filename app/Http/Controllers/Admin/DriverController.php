<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\TripPayment;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    public function driver_payments($driverId) {
        $payments = TripPayment::where("driver_id", $driverId)->orderByDESC("id")->paginate(10);
        return view('admin.drivers.payments', compact('payments'));

    }
    public function index()
    {
        $drivers = Driver::latest()->paginate(10);
        return view('admin.drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('admin.drivers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'phone'      => 'required|string|max:20',
            'salary'     => 'required',
            'address'    => 'nullable|string',
            'cnic_front' => 'nullable|image|max:2048',
            'cnic_back'  => 'nullable|image|max:2048',
            'driving_license_front' => 'nullable|image|max:2048',
            'driving_license_back'  => 'nullable|image|max:2048',
            'image' => 'nullable|image|max:2048',
        ]);

        $dir                   = "uploads/drivers/";
        $cnic_front            = "";
        $cnic_back             = "";
        $driving_license_front = "";
        $driving_license_back  = "";
        $image                 = "";


        if ($request->hasFile('cnic_front')) {
            $file     = $request->file('cnic_front');
            $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $fileName = $dir.$fileName;

            $cnic_front = asset($fileName);
        }

        if ($request->hasFile('cnic_back')) {
            $file     = $request->file('cnic_back');
            $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $fileName = $dir.$fileName;

            $cnic_back = asset($fileName);
        }

        if ($request->hasFile('driving_license_front')) {
            $file     = $request->file('driving_license_front');
            $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $fileName = $dir.$fileName;

            $driving_license_front = asset($fileName);
        }

        if ($request->hasFile('driving_license_back')) {
            $file     = $request->file('driving_license_back');
            $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $fileName = $dir.$fileName;

            $driving_license_back = asset($fileName);
        }

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $fileName = $dir.$fileName;

            $image = asset($fileName);
        }

        Driver::create([
            'name'                  => $request->name,
            'phone'                 => $request->phone,
            'salary'                => $request->salary,
            'address'               => $request->address,
            'cnic_front'            => $cnic_front,
            'cnic_back'             => $cnic_back,
            'driving_license_front' => $driving_license_front,
            'driving_license_back'  => $driving_license_back,
            'image'                 => $image,
        ]);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver added successfully.');
    }

    public function show($id)
    {
        //
    }

    public function edit(Driver $driver)
    {
         return view('admin.drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20',
            'address'     => 'nullable|string',
            'cnic_front'  => 'nullable|image|max:2048',
            'cnic_back'   => 'nullable|image|max:2048',
            'driving_license_front' => 'nullable|image|max:2048',
            'driving_license_back' => 'nullable|image|max:2048',
            'image' => 'nullable|image|max:2048',
        ]);

        $dir  = "uploads/drivers/";

        if ($request->hasFile('cnic_front')) {
            $file     = $request->file('cnic_front');
            $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $fileName = $dir.$fileName;

            $driver->cnic_front = asset($fileName);
        }

        if ($request->hasFile('cnic_back')) {
            $file     = $request->file('cnic_back');
            $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $fileName = $dir.$fileName;

            $driver->cnic_back = asset($fileName);
        }

        if ($request->hasFile('driving_license_front')) {
            $file     = $request->file('driving_license_front');
            $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $fileName = $dir.$fileName;

            $driver->driving_license_front = asset($fileName);
        }

        if ($request->hasFile('driving_license_back')) {
            $file     = $request->file('driving_license_back');
            $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $fileName = $dir.$fileName;

            $driver->driving_license_back = asset($fileName);
        }

        if ($request->hasFile('image')) {
            $file     = $request->file('image');
            $fileName = time() . '-' . uniqid() . '-driver.' . $file->getClientOriginalExtension();
            $file->move($dir, $fileName);
            $fileName = $dir.$fileName;

            $driver->image = asset($fileName);
        }

        $driver->name    = $request->name;
        $driver->phone   = $request->phone;
        $driver->address = $request->address;
        $driver->salary  = $request->salary;
        $driver->save();

        return redirect()->route('admin.drivers.index')->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        foreach (['cnic_front', 'cnic_back', 'driving_license_front', 'driving_license_back', 'image'] as $field) {
            if ($driver->$field) {
                Storage::disk('public')->delete($driver->$field);
            }
        }
        $driver->delete();

        return redirect()->route('admin.drivers.index')->with('success', 'Driver deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Vehicle;

use App\Models\TripPayment;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DriverController extends Controller
{
    public function driver_payments($driverId) {
        $payments = TripPayment::where("driver_id", $driverId)->orderByDESC("id")->paginate(50);
        return view('admin.drivers.payments', compact('payments'));

    }

    public function getSalary($id)
    {
        $driver = Driver::findOrFail($id);

        return response()->json([
            'salary' => $driver->salary
        ]);
    }

    public function index()
    {
        try {
            if (request()->ajax()) {
                $drivers = Driver::with('vehicle')->latest();
                return datatables()->eloquent($drivers->orderByDesc('id'))
                    ->addColumn('myImage', function ($data) {
                        if($data->image != null) {
                            return' <img src="'.$data->image.'" width="100" height="100" style="border-radius: 50%">';
                        } else {
                            return "";
                        }
                    })
                    ->addColumn('vehicle', function ($data) {
                            return $data->vehicle->vehicle_no ?? '';
                    })
                    ->editColumn('cnic_expiry_date', function ($data) {
                        if(isset($data->cnic_expiry_date)) {
                            return date('d m Y', strtotime($data->cnic_expiry_date));
                        } else {
                            return '';
                        }
                    })
                    ->editColumn('license_expiry_date', function ($data) {
                        if(isset($data->license_expiry_date)) {
                            return date('d m Y', strtotime($data->license_expiry_date));
                        } else {
                            return '';
                        }
                    })
                    ->editColumn('status', function ($data) {
                        return ucfirst($data->status);
                    })
                    ->addColumn('action', function ($data) {

                        $viewUrl    = route('admin.drivers.show', $data->id);
                        $editUrl    = route('admin.drivers.edit', $data->id);
                        $deleteUrl  = route('admin.drivers.destroy', $data->id);

                        return '
                            <a href="'.$viewUrl.'" class="btn btn-sm btn-info">View</a> |
                            <a href="'.$editUrl.'" class="btn btn-sm btn-warning">Edit</a> |
                            <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-sm btn-danger deleteExpenseType" onclick="return confirm(\'Are you sure?\')">Delete</button>
                            </form>';
                    })
                    ->rawColumns(['action', 'myImage', 'vehicle'])->make(true);

            }
        } catch (\Exception $ex) {
            return redirect('admin/drivers')->with('error', $ex->getMessage());
        }

        return view('admin.drivers.index');
    }

    public function create()
    {
        return view('admin.drivers.create', [
            'driver' => null
        ]);
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
            'cnic_expiry_date'      => $request->cnic_expiry_date,
            'license_expiry_date'   => $request->license_expiry_date,
            'driving_license_front' => $driving_license_front,
            'driving_license_back'  => $driving_license_back,
            'image'                 => $image,
        ]);

        return redirect()->route('admin.drivers.index')->with('success', 'Driver added successfully.');
    }

    public function show($id)
    {
        try {

            $startDate = Carbon::now()->startOfMonth();
            // Today (05 Jan)
            $endDate = Carbon::now();
            $attendances = Attendance::where('driver_id', $id)
                                        ->whereBetween('date', [
                                            $startDate->toDateString(),
                                            $endDate->toDateString()
                                        ])
                                        ->orderBy('date', 'ASC')
                                        ->get();

            $driver = Driver::findOrFail($id);
            return view("admin.drivers.show", compact('driver', "attendances"));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function edit(Driver $driver)
    {
        $vehicles = Vehicle::orderBy("vehicle_no", "ASC")->get();
        return view('admin.drivers.edit', compact('driver', 'vehicles'));
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
        $driver->vehicle_id          = $request->vehicle_id;
        $driver->name                = $request->name;
        $driver->phone               = $request->phone;
        $driver->address             = $request->address;
        $driver->salary              = $request->salary;
        $driver->cnic_expiry_date    = $request->cnic_expiry_date;
        $driver->license_expiry_date = $request->license_expiry_date;
        $driver->status              = $request->status;
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

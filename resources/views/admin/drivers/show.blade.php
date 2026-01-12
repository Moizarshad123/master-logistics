@extends('admin.layouts.app')
@section('title', 'Driver Detail')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">

<style>
    .doc-img {
        width: 120px;
        height: 100px;
        object-fit: cover;
        border: 1px solid #ddd;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
    }
    .doc-img:hover {
        transform: scale(1.05);
    }
</style>
@endsection

@section('content')

<div class="content">
    <h3>Driver Details</h3>

    <div class="card mb-3">
        <div class="card-body">
            
            {{-- Driver basic info --}}
            <div class="row mb-4">
                <div class="col-md-3">
                    <a href="{{ $driver->image }}" data-lightbox="driver">
                        <img src="{{ $driver->image }}" width="220" height="200" style="object-fit:cover; border-radius:10px;">
                    </a>
                </div>
                <div class="col-md-9" style="font-size: 18px">
                    <p><strong>Vehicle No:</strong> {{ $driver->vehicle_no }}</p>
                    <p><strong>Driver Phone No:</strong> {{ $driver->phone ?? "" }}</p>
                    <p><strong>Driver Salary:</strong> {{ $driver->salary ?? 'N/A' }}</p>
                    <p><strong>Driver Address:</strong> {{ $driver->address ?? 'N/A' }}</p>
                    <p><strong>CNIC Expiry Date:</strong> {{ $driver->cnic_expiry_date ?? 'N/A' }}</p>
                    <p><strong>License Epiry Date:</strong> {{ $driver->license_expiry_date ?? 'N/A' }}</p>

                </div>
            </div>

            {{-- Documents preview --}}
            <a href="{{ $driver->cnic_front }}" data-lightbox="docs">
                <img src="{{ $driver->cnic_front }}" class="doc-img">
            </a>
            <a href="{{ $driver->cnic_back }}" data-lightbox="docs">
                <img src="{{ $driver->cnic_back }}" class="doc-img">
            </a>
            <a href="{{ $driver->driving_license_front }}" data-lightbox="docs">
                <img src="{{ $driver->driving_license_front }}" class="doc-img">
            </a>
            <a href="{{ $driver->driving_license_back }}" data-lightbox="docs">
                <img src="{{ $driver->driving_license_back }}" class="doc-img">
            </a>
        
        </div>

        <h2>Current Month Attendance</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendances as $item)
                    <tr>
                        <td>{{ date('d M Y',strtotime($item->date)) }}</td>
                        <td>
                            @if($item->status == "present")
                                <span class="badge badge-success">Present</span>
                            @elseif($item->status == "absent")  
                                <span class="badge badge-danger">Absent</span>
                            @else
                                <span class="badge badge-warning">Leave</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
@endsection

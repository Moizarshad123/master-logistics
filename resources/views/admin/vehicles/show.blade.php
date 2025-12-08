@extends('admin.layouts.app')
@section('title', 'Vehicle Detail')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />

<style>
    .doc-box {
        width: 150px;
        margin: 10px;
        text-align: center;
        display: inline-block;
    }
    .doc-box label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }
    .doc-img {
        width: 160px;
        height: 120px;
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
    <h3>Vehicle Details</h3>

    <div class="card mb-3">
        <div class="card-body">
            
            {{-- Driver basic info --}}
                 <div class="row mb-4">
                <div class="col-md-2">
                    <a href="{{ $vehicle->image }}" data-lightbox="driver">
                        <img src="{{ $vehicle->image }}" width="200" height="220" style="object-fit:cover; border-radius:10px;">
                    </a>
                </div>
                <div class="col-md-10" style="font-size: 18px">
                    <table class="table">
                        <tr>
                            <th><strong>Vehicle No:</strong></th>
                            <td>{{ $vehicle->vehicle_no ?? "" }}</td>
                            <th><strong>Chachis No:</strong></th>
                            <td>{{ $vehicle->chachis_no ?? "" }}</td>
                        </tr>
                        <tr>
                            <th><strong>Engine No:</strong></th>
                            <td>{{ $vehicle->engine_no ?? "" }}</td>
                            <th><strong>Wheeler Type:</strong></th>
                            <td>{{ $vehicle->wheeler->name ?? "" }}</td>
                        </tr>
                        <tr>
                            <th><strong>Make:</strong></th>
                            <td>{{ $vehicle->make ?? "" }}</td>
                             <th><strong>Model:</strong></th>
                            <td>{{ $vehicle->model ?? "" }}</td>
                        </tr>
                        <tr>
                            <th><strong>Route Permit Sindh Expiry Date:</strong></th>
                            <td>{{ date('d M Y', strtotime($vehicle->route_permit_sindh_expiry)) ?? "" }}</td>
                        </tr>
                        <tr>
                            <th><strong>Route Permit Punjab Expiry Date:</strong></th>
                            <td>{{ date('d M Y', strtotime($vehicle->route_permit_punjab_expiry)) ?? "" }}</td>
                        </tr>
                          <tr>
                            <th><strong>Fitness Certificate Expiry Date:</strong></th>
                            <td>{{ date('d M Y', strtotime($vehicle->fitness_certificate_expiry)) ?? "" }}</td>
                        </tr>
                        <tr>
                            <th><strong>Insurance Certificate Expiry Date:</strong></th>
                            <td>{{ date('d M Y', strtotime($vehicle->insurance_certificate_expiry)) ?? "" }}</td>
                        </tr>
                        <tr>
                            <th><strong>Tax Token Expiry Date:</strong></th>
                            <td>{{ date('d M Y', strtotime($vehicle->tax_token_expiry)) ?? "" }}</td>
                        </tr>
                    </table>
                    
                </div>
            </div>

            <div class="docs-container">

                <div class="doc-box">
                    <label>Route Permit Sindh</label>
                    <a href="{{ $vehicle->route_permit_sindh }}" data-lightbox="docs">
                        <img src="{{ $vehicle->route_permit_sindh }}" class="doc-img">
                    </a>
                </div>

                <div class="doc-box">
                    <label>Route Permit Punjab</label>
                    <a href="{{ $vehicle->route_permit_punjab }}" data-lightbox="docs">
                        <img src="{{ $vehicle->route_permit_punjab }}" class="doc-img">
                    </a>
                </div>

                <div class="doc-box">
                    <label>Fitness Certificate</label>
                    <a href="{{ $vehicle->fitness_certificate }}" data-lightbox="docs">
                        <img src="{{ $vehicle->fitness_certificate }}" class="doc-img">
                    </a>
                </div>

                <div class="doc-box">
                    <label>Insurance Certificate</label>
                    <a href="{{ $vehicle->insurance_certificate }}" data-lightbox="docs">
                        <img src="{{ $vehicle->insurance_certificate }}" class="doc-img">
                    </a>
                </div>

                <div class="doc-box">
                    <label>Tax Token</label>
                    <a href="{{ $vehicle->tax_token }}" data-lightbox="docs">
                        <img src="{{ $vehicle->tax_token }}" class="doc-img">
                    </a>
                </div>

                <div class="doc-box">
                    <label>Vehicle File</label>
                    <a href="{{ $vehicle->vehicle_file }}" data-lightbox="docs">
                        <img src="{{ $vehicle->vehicle_file }}" class="doc-img">
                    </a>
                </div>

            </div>
        
        </div>
    </div>

@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
@endsection

@extends('admin.layouts.app')
@section('title', 'Trip Vehicle Report')

@section('content')

<div class="content">
    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Trip Vehicle Report</h3>
            </div>
            <div class="col-md-2">
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="ordersTable">
            <thead>
                <tr>
                    <th>Trip No</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Total Journeys</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trips as $trip)
                    <tr>
                        <td>{{ $trip->trip_no }}</td>
                        <td>{{ $trip->vehicle->vehicle_no ?? '-' }}</td>
                        <td>{{ $trip->driver->name ?? '-' }}</td>
                        <td>{{ $trip->tripDetails->count() }}</td>
                        <td>{{ $trip->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.viewTripVehicleReport', $trip->id) }}" class="btn btn-sm btn-info">View Report</a>                           
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    {{ $trips->links() }}
</div>
@endsection

@section('js')


@endsection

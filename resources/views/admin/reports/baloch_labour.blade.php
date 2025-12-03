@extends('admin.layouts.app')
@section('title', 'Baloch Labour Report')

@section('content')

<div class="content">

    <div class="row">
        <div class="col-md-10">
            <h3>Baloch Labour Report</h3>
        </div>
        <div class="col-md-2">
            <a href="{{ route("admin.viewBalochLabourReport") }}" class="btn btn-info">View Report</a>
        </div>
    </div>

    <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.weeklyLabourReport') }}" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label>Trip No</label>
                <input type="text" name="trip_no" class="form-control" value="{{ request('trip_no') }}">
            </div>
            <div class="col-md-4">
                <label>Vehicle No</label>
                <input type="text" name="vehicle_no" class="form-control" value="{{ request('vehicle_no') }}">
            </div>
            <div class="col-md-4">
                <label>Date Range</label>
                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Select date range">
            </div>
      
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.weeklyLabourReport') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
    
    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0">
            <thead>
                <tr>
                    <th>Trip No</th>
                    <th>Vehicle</th>
                    <th>Trip Type</th>
                    <th>Material</th>
                    <th>Total Bags</th>
                    <th>Baloch Labour Rate</th>
                    <th>Baloch Labour</th>
                    <th>No of Labour</th>
                    <th>Rent</th>
                    {{-- <th>Start Date</th> --}}
                    {{-- <th>End Date</th> --}}
                    {{-- <th style="text-align: center">From & To Destination</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $trip)
                    <tr>
                        <td>{{ $trip->trip_id }}</td>
                        <td>{{ $trip->trip->vehicle->vehicle_no ?? '-' }}</td>
                        <td>{{ $trip->trip_type ?? '-' }}</td>
                        <td>{{ $trip->material ?? '-' }}</td>
                        <td>{{ $trip->total_bags ?? '-' }}</td>
                        <td>{{ $trip->baloch_labour_rate ?? ""}}</td>
                        <td>{{ $trip->baloch_labour ?? ""}}</td>
                        <td>{{ $trip->no_of_labour ?? ""}}</td>
                        <td>{{ $trip->rent}}</td>
                        {{-- <td>{{ date('d M Y', strtotime($trip->start_date)) ?? "" }}</td> --}}
                        {{-- <td>{{ date('d M Y', strtotime($trip->end_date)) ?? "" }}</td> --}}
                        {{-- <td>{{ $trip->from_destination.' - '.$trip->to_destination ?? '-' }}</td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $reports->links() }}
</div>
@endsection

@section('js')
@endsection

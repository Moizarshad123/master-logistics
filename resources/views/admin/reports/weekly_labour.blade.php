@extends('admin.layouts.app')
@section('title', 'Weekly Labour Report')

@section('content')

<div class="content">
    <div class="mb-4">
        <h3>Weekly Labour Report</h3>
        <a href="{{ route("admin.viewWeeklyLabourReport") }}" class="btn btn-info">View Report</a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0">
            <thead>
                <tr>
                    <th>Trip No</th>
                    <th>Vehicle</th>
                    {{-- <th>Customer</th> --}}
                    <th>Trip Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th style="text-align: center">From & To Destination</th>
                    <th>Material</th>
                    <th>Total Bags</th>
                    <th>Weekly Labour Rate</th>
                    <th>Weekly Labour</th>
                    {{-- <th>No of Labour</th> --}}
                    {{-- <th>Rent</th> --}}
                    {{-- <th>Action</th> --}}
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $item)
                    <tr>
                        <td>{{ $item->trip_id }}</td>
                        <td>{{ $item->trip->vehicle->vehicle_no ?? '-' }}</td>
                        {{-- <td>{{ $item->customer->name ?? '-' }}</td> --}}
                        <td>{{ $item->trip_type ?? '-' }}</td>
                        <td>{{ date('d M Y', strtotime($item->start_date)) ?? "" }}</td>
                        <td>{{ date('d M Y', strtotime($item->end_date)) ?? "" }}</td>
                        <td>{{ $item->from_destination.' - '.$item->to_destination ?? '-' }}</td>
                        <td>{{ $item->material ?? '-' }}</td>
                        <td>{{ $item->total_bags ?? '-' }}</td>
                        <td>{{ $item->rate ?? ""}}</td>
                        <td>{{ $item->weekly_labour ?? ""}}</td>
                        {{-- <td>{{ $item->no_of_labour ?? ""}}</td> --}}
                        {{-- <td>{{ $item->rent}}</td> --}}
                        {{-- <th></th> --}}
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

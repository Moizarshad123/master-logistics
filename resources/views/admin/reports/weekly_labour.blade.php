@extends('admin.layouts.app')
@section('title', 'Weekly Labour Report')


@section("css")
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection
@section('content')

    <div class="content">
        <div class="mb-4">
            <div class="row">
                <div class="col-md-10">
                    <h3>Weekly Labour Report</h3>
                </div>
                <div class="col-md-2">
                    <a href="{{ route("admin.viewWeeklyLabourReport") }}" class="btn btn-info">View Report</a>
                </div>
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
                    <input type="text" name="date_range" id="date_range" class="form-control" value="{{ request('date_range') }}" placeholder="Select date range">
                    {{-- <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Select date range"> --}}
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
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th style="text-align: center">From & To Destination</th>
                        <th>Material</th>
                        <th>Total Bags</th>
                        <th>Weekly Labour Rate</th>
                        <th>Weekly Labour</th>
                        {{-- <th>Customer</th> --}}
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
                            <td>{{ $item->trip_type ?? '-' }}</td>
                            <td>{{ date('d M Y', strtotime($item->start_date)) ?? "" }}</td>
                            <td>{{ date('d M Y', strtotime($item->end_date)) ?? "" }}</td>
                            <td>{{ $item->from_destination.' - '.$item->to_destination ?? '-' }}</td>
                            <td>{{ $item->material ?? '-' }}</td>
                            <td>{{ $item->total_bags ?? '-' }}</td>
                            <td>{{ $item->rate ?? ""}}</td>
                            <td>{{ $item->weekly_labour ?? ""}}</td>
                            {{-- <td>{{ $item->customer->name ?? '-' }}</td> --}}
                            {{-- <td>{{ $item->no_of_labour ?? ""}}</td> --}}
                            {{-- <td>{{ $item->rent}}</td> --}}
                            {{-- <th></th> --}}
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="float:right">
        {{ $reports->links() }}
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $('input[name="date_range"]').daterangepicker();
    </script>
@endsection

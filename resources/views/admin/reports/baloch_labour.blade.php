@extends('admin.layouts.app')
@section('title', 'Baloch Labour Report')

@section('content')

<div class="content">
    <div class="mb-4">
        <h3>Baloch Labour Report</h3>
    </div>
    
    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0">
            <thead>
                <tr>
                    <th>Trip No</th>
                    <th>Customer</th>
                    <th>Trip Type</th>
                    {{-- <th>Start Date</th> --}}
                    {{-- <th>End Date</th> --}}
                    {{-- <th style="text-align: center">From & To Destination</th> --}}
                    <th>Material</th>
                    <th>Total Bags</th>
                    <th>Baloch Labour Rate</th>
                    <th>Baloch Labour</th>
                    <th>No of Labour</th>
                    <th>Rent</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $trip)
                    <tr>
                        <td>{{ $trip->trip_id }}</td>
                        <td>{{ $trip->customer->name ?? '-' }}</td>
                        <td>{{ $trip->trip_type ?? '-' }}</td>
                        {{-- <td>{{ date('d M Y', strtotime($trip->start_date)) ?? "" }}</td> --}}
                        {{-- <td>{{ date('d M Y', strtotime($trip->end_date)) ?? "" }}</td> --}}
                        {{-- <td>{{ $trip->from_destination.' - '.$trip->to_destination ?? '-' }}</td> --}}
                        <td>{{ $trip->material ?? '-' }}</td>
                        <td>{{ $trip->total_bags ?? '-' }}</td>
                        <td>{{ $trip->baloch_labour_rate ?? ""}}</td>
                        <td>{{ $trip->baloch_labour ?? ""}}</td>
                        <td>{{ $trip->no_of_labour ?? ""}}</td>
                        <td>{{ $trip->rent}}</td>
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

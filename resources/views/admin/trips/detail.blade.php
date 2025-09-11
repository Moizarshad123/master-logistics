@extends('admin.layouts.app')
@section('title', 'Trip Detail')

@section('content')

<div class="content">
    <h3>Trip Details</h3>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Trip No:</strong> {{ $trip->trip_no }}</p>
            <p><strong>Vehicle:</strong> {{ $trip->vehicle->vehicle_no ?? 'N/A' }}</p>
            <p><strong>Driver:</strong> {{ $trip->driver->name ?? 'N/A' }}</p>
        </div>
    </div>

    <h5>Trip Detail Records</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Start Date</th>
                <th>End Date</th>
                <th>From</th>
                <th>To</th>
                <th>Material</th>
                <th>Total Bags</th>
                <th>Loading Labour</th>
                <th>Unloading Labour</th>
                <th>Rent</th>
                <th>Advance</th>
                <th>Weight</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trip->tripDetails as $detail)
            <tr>
                <td>{{ date('d M Y', strtotime($detail->start_date)) }}</td>
                <td>{{ date('d M Y', strtotime($detail->end_date)) }}</td>
                <td>{{ $detail->from_destination }}</td>
                <td>{{ $detail->to_destination }}</td>
                <td>{{ $detail->material }}</td>
                <td>{{ $detail->total_bags }}</td>
                <td>{{ $detail->loading_labour }}</td>
                <td>{{ $detail->unloading_labour }}</td>
                <td>{{ $detail->rent }}</td>
                <td>{{ $detail->advance }}</td>
                <td>{{ $detail->weight }}</td>
                <td>{{ ucfirst($detail->status) }}</td>
            </tr>
            @empty
            <tr><td colspan="12" class="text-center">No trip details added.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h5 style="margin-top: 20px;">Trip Expenses</h5>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Expense Name</th>
                <th>Expense Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trip->tripExpenses as $expense)
                <tr>
                    <td>{{ $expense->expenseName->name ?? "" }}</td>
                    <td>{{ $expense->amount }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="text-center">No expenses added.</td></tr>
            @endforelse
        </tbody>
    </table>


    <a style="margin-top: 20px;" href="{{ route('admin.trips.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection

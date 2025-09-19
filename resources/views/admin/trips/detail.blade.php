@extends('admin.layouts.app')
@section('title', 'Trip Detail')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
@endsection
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

    <h5>Trip Payments</h5>
    <div style="max-height: 300px; overflow-y: auto;">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Payment Type</th>
                    <th>Payment Date</th>
                    <th>Amount</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trip->tripPayments as $detail)
                    <tr>
                        <td>{{ $detail->payment_type }}</td>
                        <td>{{ date('d M Y', strtotime($detail->date)) ?? "" }}</td>
                        <td>{{ number_format($detail->amount) }}</td>
                        <td>{{ $detail->comments }}</td>
                        
                    </tr>
                @empty
                    <tr><td colspan="12" class="text-center">No trip details added.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>



    <h5>Trip Detail Records</h5>
    <div style="max-height: 300px; overflow-y: auto;">
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
                    {{-- <th>Advance</th> --}}
                    <th>Weight</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trip->tripDetails as $detail)
                    <tr>
                        <td>{{ date('d M Y', strtotime($detail->start_date)) ?? "" }}</td>
                        <td>{{ isset($detail->end_date) ? date('d M Y', strtotime($detail->end_date)) : ""}}</td>
                        <td>{{ $detail->from_destination }}</td>
                        <td>{{ $detail->to_destination }}</td>
                        <td>{{ $detail->material }}</td>
                        <td>{{ $detail->total_bags }}</td>
                        <td>{{ $detail->loading_labour }}</td>
                        <td>{{ $detail->unloading_labour }}</td>
                        <td>{{ $detail->rent }}</td>
                        {{-- <td>{{ $detail->advance }}</td> --}}
                        <td>{{ $detail->weight }}</td>
                        <td>
                            @if($detail->status == 'Started')
                                <span class="badge badge-primary">Started</span>
                            @elseif($detail->status == 'Ended')
                                <span class="badge badge-success">Ended</span>
                            @endif
                        </td>

                        <td>
                            @if($detail->status == "Started")
                                <button type="button" data-trip-id="{{ $detail->id }}"  class="btn btn-danger btn-sm endTripBtn">End Trip</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="text-center">No trip details added.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

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
                    <td>{{ $expense->expense ?? "" }}</td>
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

@section('js')
<script>
    $(document).ready(function () {
        $('.endTripBtn').on('click', function () {
            const tripId = $(this).data('trip-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to end this trip?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, end it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('admin/endtrip') }}/${tripId}`,
                        type: 'GET',
                        data: {
                            _token: '{{ csrf_token() }}' // Required for Laravel POST
                        },
                        success: function (response) {
                            Swal.fire(
                                'Trip Ended!',
                                'The trip has been successfully ended.',
                                'success'
                            );

                            // Optional: Update UI or disable button
                            $('#endTripBtn').prop('disabled', true).text('Trip Ended');
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        },
                        error: function (xhr) {
                            Swal.fire(
                                'Error!',
                                'Something went wrong. Please try again.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });

</script>
@endsection


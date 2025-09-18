@extends('admin.layouts.app')
@section('title', 'Edit Trip')

@section('content')
<div class="content">
    <h3>Edit Trip</h3>
    <form action="{{ route('admin.trips.update', $trip->id) }}" method="POST" id="expenseTypeForm">
        @csrf @method('PUT')

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Trip No</label>
                <input type="text" name="trip_no" value="{{ old('trip_no', $trip->trip_no) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label>Vehicle</label>
                <select name="vehicle_id" class="form-control" required>
                    <option value="">Select Vehicle</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ $vehicle->id == $trip->vehicle_id ? 'selected' : '' }}>
                            {{ $vehicle->vehicle_no }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Driver</label>
                <select name="driver_id" class="form-control" required>
                    <option value="">Select Driver</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ $driver->id == $trip->driver_id ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="vehicleExpensesContainer" class="mt-3">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Expense</th>
                        <th>Expense Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $expense)
                        <tr>
                            <td>{{$expense->expenseName->name ?? ""}}</td>
                            <td>
                                <input type="number" step="0.01" 
                                    name="expenses[{{$expense->id}}][amount]" 
                                    class="form-control"  value="{{ $expense->amount }}">
                                <input type="hidden" 
                                    name="expenses[{{$expense->id}}][expense_type_id]" 
                                    value="{{ $expense->id }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <h5>Trip Details</h5>
        <div id="tripDetailsContainer">
            @foreach($trip->tripDetails as $key => $detail)
                @include('admin.trips.trip-detail-row', ['index' => $key, 'detail' => $detail])
            @endforeach
        </div>

        <button type="button" class="btn btn-sm btn-success" id="addRow">+ Add Detail</button>
        <br><br>
        <button type="submit" class="btn btn-primary" id="addExpenseType">Update Trip</button>
    </form>
</div>
@endsection


@section('js')
<script>
    $(document).ready(function () {
        let index = $("#tripDetailsContainer .trip-detail").length;

        $("#addRow").click(function (e) {
            e.preventDefault();
            let row = `
            <div class="trip-detail border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <label>Start Date</label>
                        <input type="date" name="trip_details[${index}][start_date]" value="{{date('Y-m-d')}}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>From</label>
                        <input type="text" name="trip_details[${index}][from_destination]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>To</label>
                        <input type="text" name="trip_details[${index}][to_destination]" class="form-control">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <label>Material</label>
                        <input type="text" name="trip_details[${index}][material]" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Total Bags</label>
                        <input type="number" name="trip_details[${index}][total_bags]" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Loading Labour</label>
                        <input type="text" name="trip_details[${index}][loading_labour]" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Unloading Labour</label>
                        <input type="text" name="trip_details[${index}][unloading_labour]" class="form-control">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <label>Rent</label>
                        <input type="number" name="trip_details[${index}][rent]" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Advance</label>
                        <input type="number" name="trip_details[${index}][advance]" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Weight</label>
                        <input type="number" step="0.01" name="trip_details[${index}][weight]" class="form-control">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="button" class="btn btn-danger removeRow">Remove</button>
                    </div>
                </div>
            </div>`;

            $("#tripDetailsContainer").append(row);
            index++;
        });

        $(document).on("click", ".removeRow", function () {
            $(this).closest(".trip-detail").remove();
        });

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
                        url: `/admin/endtrip/${tripId}`,
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

        $('#addExpenseType').on('click', function (e) {

            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to update this trip?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                  $('#expenseTypeForm').submit();
                }
            });
        });
    });

</script>
@endsection

@extends('admin.layouts.app')
@section('title', 'Edit Trip')

@section('css')
<!-- In head -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="content">
    <h3>Edit Trip</h3>
    <form action="{{ route('admin.trips.update', $trip->id) }}" method="POST" id="expenseTypeForm">
        @csrf @method('PUT')
        <div class="row mb-3">
            <div class="col-md-3">
                <label>Trip Type<span style="color: red">*</span></label>
                <select name="trip_type" class="form-select" required>
                    <option value="">Select Trip Type</option>
                    <option value="Commercial" {{ $trip->trip_type == "Commercial" ? "selected" : "" }}>Commercial</option>
                    <option value="Purchase" {{ $trip->trip_type == "Purchase" ? "selected" : "" }}>Purchase</option>
                    <option value="Sell" {{ $trip->trip_type == "Sell" ? "selected" : "" }}>Sell</option>
                    <option value="Local" {{ $trip->trip_type == "Local" ? "selected" : "" }}>Local</option>
                    {{-- @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                    @endforeach --}}
                </select>
            </div>
            <div class="col-md-3">
                <label>Vehicle</label>
                <select name="vehicle_id" class="form-control select2" required>
                    <option value="">Select Vehicle</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" {{ $vehicle->id == $trip->vehicle_id ? 'selected' : '' }}>
                            {{ $vehicle->vehicle_no }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Driver</label>
                <select name="driver_id" class="form-control select2" required>
                    <option value="">Select Driver</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ $driver->id == $trip->driver_id ? 'selected' : '' }}>
                            {{ $driver->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>Balance</label>
                <input type="text" name="balance" class="form-control" readonly id="balance" value="{{ $trip->balance }}">
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="mb-0">Trip Payments</h5>
                    </div>
                    <div class="col text-end">
                        <button class="btn btn-success" id="addTripExpense">+</button>
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Payment Mode</th>
                            <th>Amount</th>
                            <th>Payment Date</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody id="expensePaymentTable">
                            @foreach ($payments as $item)
                                <input type="hidden" name="payment_id[]" value="{{ $item->id }}">
                                <tr>
                                    <td>
                                        <select name="payment_type[]" class="form-select">
                                            <option value="Cash"  {{ $item->payment_type == "Cash" ? "selected" : ''}}>Cash</option>
                                            <option value="Jazz Cash" {{ $item->payment_type == "Jazz Cash" ? "selected" : ''}}>Jazz Cash</option>
                                            <option value="Easypaisa" {{ $item->payment_type == "Easypaisa" ? "selected" : ''}}>Easypaisa</option>
                                            <option value="Bank Transfer" {{ $item->payment_type == "Cash" ? "Bank Transfer" : ''}}>Bank Transfer</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="expense_amount[]" class="form-control" value="{{ $item->amount ?? ''}}">
                                    </td>
                                    <td>
                                        <input type="date" name="date[]"  class="form-control" value="{{ $item->date ?? ''}}">
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="comments[]">{{ $item->comments ?? ''}}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                    </tbody>
                </table>
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
                <tbody id="expensesTableBody">
                    @foreach ($expenses as $expense)
                        <tr>
                            <td>
                                <input type="text" name="expenses[{{$expense->id}}][name]" value="{{$expense->expense ?? ""}}" class="form-control" readonly>
                            </td>
                            <td>
                                <input type="number" step="0.01" 
                                    name="expenses[{{$expense->id}}][amount]" 
                                    class="form-control"  value="{{ $expense->amount }}">

                                <input type="hidden" name="expenses[{{$expense->id}}][id]" value="{{$expense->id}}">
                                {{-- <input type="hidden" 
                                    name="expenses[{{$expense->id}}][expense_type_id]" 
                                    value="{{ $expense->id }}"> --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="row align-items-center mb-2" style="margin-top: 25px">
            <div class="col-md-9">
                <h5 class="mb-0">Trip Details</h5>
            </div>
            <div class="col-md-3 text-end">
                <button type="button" class="btn btn-sm btn-warning" id="addExpenseRow">
                    + Add More Expenses
                </button>
            </div>
        </div>  

        <div id="tripDetailsContainer">
            @foreach($trip->tripDetails as $key => $detail)
                @include('admin.trips.trip-detail-row', ['index' => $key, 'detail' => $detail])
            @endforeach
        </div>
        <div class="row">
            <div class="col text-end">
                <button type="button" class="btn btn-sm btn-success" id="addRow">+ Add Detail</button>
            </div>
        </div>
        <br><br>
        <button type="submit" class="btn btn-success" id="addExpenseType">Update Trip</button>
    </form>
</div>
@endsection


@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select an option",
            allowClear: true,
            width: '100%'
        });
    });
</script>
<script>
    $(document).ready(function () {
        let index = $("#tripDetailsContainer .trip-detail").length;
        let extraExpenseIndex = 1000;
        calculateBalance();

         // Generate expense options from $expenses
        const expenseOptions = `
                                <option value="">Select Expense</option>
                                @foreach($expensesTypes as $expense)
                                    <option value="{{ $expense->id }}">{{ $expense->name }}</option>
                                @endforeach
                            `;


        function calculateBalance() {
            let totalPayments = 0;
            let totalExpenses = 0;

            // Sum all payment amounts
            $("input[name='expense_amount[]']").each(function () {
                const val = parseFloat($(this).val());
                if (!isNaN(val)) {
                    totalPayments += val;
                }
            });

            // Sum all expense amounts
            $("input[name^='expenses'][name$='[amount]']").each(function () {
                const val = parseFloat($(this).val());
                if (!isNaN(val)) {
                    totalExpenses += val;
                }
            });

            const balance = totalPayments - totalExpenses;

            if (balance < 0) {
                $("#balance").addClass("text-danger").removeClass("text-success");
            } else {
                $("#balance").addClass("text-success").removeClass("text-danger");
            }
            $("#balance").val(balance.toFixed(2));
        }
        
        $(document).on("input", "input[name='expense_amount[]'], input[name^='expenses'][name$='[amount]']", calculateBalance);
        $("#addExpenseRow").click(function (e) {
            e.preventDefault();
            const newRow = `
                    <tr>
                        <td>
                            <select name="expenses[${extraExpenseIndex}][name]" class="form-select">
                                ${expenseOptions}
                            </select>
                        </td>
                        <td>
                            <input type="number" step="0.01" 
                                name="expenses[${extraExpenseIndex}][amount]" 
                                class="form-control" 
                                placeholder="Enter amount">
                        </td>
                    </tr>
                `;

                $("#expensesTableBody").append(newRow);
                extraExpenseIndex++;
                 calculateBalance();

        });

        $("#addTripExpense").click(function (e) {
            e.preventDefault();
            const newRow = `
                    <tr>
                        <td>
                            <select name="payment_type[]" class="form-select">
                                <option value="Cash">Cash</option>
                                <option value="Jazz Cash">Jazz Cash</option>
                                <option value="Easypaisa">Easypaisa</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" step="0.01" name="expense_amount[]" class="form-control" placeholder="Enter amount">
                        </td>
                        <td>
                            <input type="date" name="date[]"  class="form-control" value="{{ date('Y-m-d') }}">
                        </td>
                        <td>
                            <textarea class="form-control" name="comments[]"></textarea>
                        </td>
                    </tr>`;

                $("#expensePaymentTable").append(newRow);

            calculateBalance();

        });

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
                    <div class="col-md-4">
                        <label>Material</label>
                        <input type="text" name="trip_details[${index}][material]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Material Type</label>
                        <select class="form-select" name="trip_details[${index}][material_type]">
                            <option value="">Select Material Type</option>
                            <option value="Bags">Bags</option>
                            <option value="Tainki">Tainki</option>
                            <option value="LTR">LTR</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Total Bags</label>
                        <input type="number" name="trip_details[${index}][total_bags]" class="form-control">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <label>Weekly Labour</label>
                        <input type="text" name="trip_details[${index}][weekly_labour]" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Baloch Labour</label>
                        <input type="text" name="trip_details[${index}][baloch_labour]" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Loading Labour</label>
                        <input type="text" name="trip_details[${index}][loading_labour]" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Unloading Labour</label>
                        <input type="text" name="trip_details[${index}][unloading_labour]" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Rent</label>
                        <input type="number" name="trip_details[${index}][rent]" class="form-control">
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
            //   <div class="col-md-3">
            //             <label>Advance</label>
            //             <input type="number" name="trip_details[${index}][advance]" class="form-control">
            //         </div>

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

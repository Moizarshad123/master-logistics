@extends('admin.layouts.app')
@section('title', 'Add Trip')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h3>Create Trip</h3>
        <form action="{{ route('admin.trips.store') }}" method="POST" id="expenseTypeForm">
            @csrf

            <div class="row mb-3">
               
                <div class="col-md-4">
                    <label>Vehicle<span style="color: red">*</span></label>
                    <select name="vehicle_id" id="vehicle_id" class="form-control" required>
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_no }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Driver<span style="color: red">*</span></label>
                    <select name="driver_id" class="form-control" required>
                        <option value="">Select Driver</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Balance</label>
                    <input type="text" name="balance" class="form-control" readonly id="balance">
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
                            
                        </tbody>
                    </table>
                </div>
            </div>


            <div id="vehicleExpensesContainer" class="mt-3">
                <!-- Expenses will load here -->
            </div>

            <div class="row align-items-center mb-2" style="margin-top: 25px">
                <div class="col-md-7">
                    <h5 class="mb-0">Trip Details</h5>
                </div>
                <div class="col-md-2 text-end">
                    <button type="button" class="btn btn-sm btn-success" id="addRow">
                        + Add Trip Detail
                    </button>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-sm btn-warning" id="addExpenseRow">
                        + Add More Expenses
                    </button>
                </div>
            </div>   
            <div class="card">
                <div class="card-body">
                    <div id="tripDetailsContainer">
                        <!-- Trip detail rows will be appended here -->
                    </div>
                </div>
            </div>         


            <button type="submit" class="btn btn-success" id="addExpenseType">Save Trip</button>
        </form>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        let index = 0;
        let extraExpenseIndex = 1000;

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

        // Watch for changes in both payments and expenses
        $(document).on("input", "input[name='expense_amount[]'], input[name^='expenses'][name$='[amount]']", calculateBalance);


        $("#addExpenseRow").click(function (e) {
            e.preventDefault();
            const newRow = `
                    <tr>
                        <td>
                            <input type="text" 
                                name="expenses[${extraExpenseIndex}][name]" 
                                class="form-control" 
                                placeholder="Enter expense name">
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
                    <div class="col-md-4">
                        <label>Rent</label>
                        <input type="number" name="trip_details[${index}][rent]" class="form-control">
                    </div>
                    
                    <div class="col-md-4">
                        <label>Weight</label>
                        <input type="number" step="0.01" name="trip_details[${index}][weight]" class="form-control">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-danger removeRow">Remove</button>
                    </div>
                </div>
            </div>`;

            $("#tripDetailsContainer").append(row);
            index++;
            // <div class="col-md-3">
            //             <label>Advance</label>
            //             <input type="number" name="trip_details[${index}][advance]" class="form-control">
            //         </div>
        });

        $(document).on("click", ".removeRow", function () {
            $(this).closest(".trip-detail").remove();
        });


        $("#vehicle_id").change(function() {
            let vehicleId = $(this).val();

            if (vehicleId) {
                $.ajax({
                    url: "{{ route('admin.getVehicleExpenses') }}",
                    type: "GET",
                    data: { vehicle_id: vehicleId },
                    success: function(response) {
                        $("#vehicleExpensesContainer").html(response);
                    },
                    error: function() {
                        alert("Unable to fetch vehicle expenses.");
                    }
                });
            } else {
                $("#vehicleExpensesContainer").empty();
            }
        });

        $('#addExpenseType').on('click', function (e) {

            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to add this trip?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, add it!'
            }).then((result) => {
                if (result.isConfirmed) {
                  $('#expenseTypeForm').submit();
                }
            });
        });
    });

</script>
@endsection

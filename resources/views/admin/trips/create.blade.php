@extends('admin.layouts.app')
@section('title', 'Add Trip')

@section('css')
<!-- In head -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h3 style="margin-bottom: 35px">Create Trip</h3>
        <form action="{{ route('admin.trips.store') }}" method="POST" id="expenseTypeForm">
            @csrf

            <div class="row mb-4">
                <div class="col-md-3">
                    <label>Trip Type<span style="color: red">*</span></label>
                    <select name="trip_type" id="trip_type" class="form-select" required>
                        <option value="">Select Trip Type</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Purchase">Purchase</option>
                        <option value="Feed Sell">Feed Sell</option>
                        <option value="Other Sell">Other Sell</option>
                        <option value="Local">Local</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Trip Date<span style="color: red">*</span></label>
                    <input type="date" name="trip_date" value="{{ date('Y-m-d')}}" class="form-control">
                </div>
               
                <div class="col-md-3">
                    <label>Vehicle<span style="color: red">*</span></label>
                    <select name="vehicle_id" id="vehicle_id" class="form-select select2" required>
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_no }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Driver<span style="color: red">*</span></label>
                    <select name="driver_id" class="form-select select2" required>
                        <option value="">Select Driver</option>
                        @foreach($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                {{-- <div class="col-md-6">
                    <label>Balance</label>
                    <input type="text" name="balance" class="form-control" readonly id="balance">
                </div> --}}
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row" style="margin-bottom: 25px">
                        <div class="col-md-3">
                            <h5 class="mb-0">Trip Payments</h5>
                        </div>
                        <div class="col-md-4">
                            <label>Balance</label>
                            <input type="text" name="balance" class="form-control" readonly id="balance">
                        </div>
                        <div class="col-md-5 text-end">
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
                                <th>Action</th>
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
                <div class="col-md-9">
                    <h5 class="mb-0">Trip Details</h5>
                   
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" class="btn btn-sm btn-success" id="addRow">
                        + Add Trip Detail
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
            <button style="margin-top: 50px;" type="submit" class="btn btn-success" id="addExpenseType">Save Trip</button>
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


          // Generate expense options from $expenses
        const expenseOptions = `
                                <option value="">Select Expense</option>
                                @foreach($expenses as $expense)
                                    <option value="{{ $expense->id }}">{{ $expense->name }}</option>
                                @endforeach
                            `;

        $(document).on('click', "#addExpenseRow", function(e) {
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
                         <td>
                            <select name="expenses[${extraExpenseIndex}][expense_from]" class="form-select">
                                <option value="">Select Expense From</option>
                                @foreach ($expense_froms as $item)
                                    <option value="{{ $item->name }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="text-center">
                            <button type="button" style="border-radius: 25%;" class="btn btn-danger btn-sm removeExpenseRow">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;

                $("#expensesTableBody").append(newRow);
                extraExpenseIndex++;

                calculateBalance();

        });

        $(document).on("click", ".removeExpenseRow", function () {
            $(this).closest("tr").remove();
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
                        <td class="text-center">
                            <button type="button" style="border-radius: 25%;" class="btn btn-danger btn-sm removeTripPaymentRow">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>`;

                $("#expensePaymentTable").append(newRow);

            calculateBalance();

        });

        $(document).on("click", "#addRow", function (e) {

            e.preventDefault();
            let tripType = $("#trip_type option:selected").val();

            if (!tripType) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Please select Trip type to continue!",
                });
                return; // stop the function, do not append row
            }
            let to ="";
            from = "";
            if(tripType == "Feed Sell") {

                from = `<input type="text" class="form-control" name="trip_details[${index}][from_destination]" value="Master Agro" readonly>`;

                to = `<select class="form-select" name="trip_details[${index}][to_destination]" style="display: ${tripType === 'Purchase' ? 'none' : 'block'};">
                            <option value="">Select To Station</option>
                            @foreach ($sales as $item)
                                <option value="{{ $item->id }}">{{ $item->station }}</option>
                            @endforeach
                        </select>`;
            } else if (tripType === "Purchase") {
                // For Purchase
                to = `<input type="text" class="form-control" name="trip_details[${index}][from_destination]" value="Master Agro" readonly>`;
                
                from = `<select class="form-select" name="trip_details[${index}][to_destination]">
                        <option value="">Select From Station</option>
                        @foreach ($purchases as $item)
                            <option value="{{ $item->id }}">{{ $item->station }}</option>
                        @endforeach
                    </select>`;
            } else {
                // For all other trip types
                from = `<select class="form-select" name="trip_details[${index}][from_destination]">
                            <option value="">Select From Destination</option>
                            @foreach ($destinations as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>`;
                
                to = `<select class="form-select" name="trip_details[${index}][to_destination]">
                            <option value="">Select To Destination</option>
                            @foreach ($destinations as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                    </select>`;
            }
            let row = `
            <div class="trip-detail border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <label>Start Date</label>
                        <input type="date" name="trip_details[${index}][start_date]" value="{{date('Y-m-d')}}" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>From</label>
                       `+from+`

                    </div>
                    <div class="col-md-4">
                        <label>To</label>
                        `+to+`
                    </div>
                </div>
                <div class="row mt-2 trip-row">    
                  
                    <div class="col-md-3">
                        <label>Material Type</label>
                        <select class="form-select" name="trip_details[${index}][material_type]">
                            <option value="Bags">Bags</option>
                            <option value="Tainki">Tainki</option>
                            <option value="LTR">LTR</option>
                        </select>
                    </div>
                     <div class="col-md-3">
                        <label>Material</label>
                        
                        <select class="form-select" name="trip_details[${index}][material]">
                            <option value="">Select Material</option>
                            @foreach ($materials as $material)
                                <option value="{{ $material->name }}">{{ $material->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 baloch-labour-field" style="display: ${tripType === 'Feed Sell' ? 'block' : 'none'};">
                        <label>Baloch Labour Rate</label>
                        <input type="text" name="trip_details[${index}][baloch_labour_rate]" class="form-control baloch-labour-rate">
                    </div>
                    <div class="col-md-3 baloch-labour-field" style="display: ${tripType === 'Feed Sell' ? 'block' : 'none'};">
                        <label>Baloch Labour</label>
                        <input type="text" name="trip_details[${index}][baloch_labour]" class="form-control baloch-labour">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <label>Total Bags</label>
                        <input type="number" name="trip_details[${index}][total_bags]" class="form-control total-bags">
                    </div>

                    <div class="col-md-3">
                        <label>Labour Rate</label>
                        <input type="text" name="trip_details[${index}][rate]" class="form-control rate">
                    </div>

                    <div class="col-md-3">
                        <label>No Of Labour</label>
                        <input type="text" name="trip_details[${index}][no_of_labour]" class="form-control no_of_labour">
                    </div>
                    

                    <div class="col-md-3">
                        <label>Weekly Labour</label>
                        <input type="text" name="trip_details[${index}][weekly_labour]" class="form-control weekly-labour">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <label>Rent</label>
                        <input type="number" name="trip_details[${index}][rent]" class="form-control">
                    </div>
                    
                    <div class="col-md-3">
                        <label>Weight</label>
                        <input type="number" step="0.01" name="trip_details[${index}][weight]" class="form-control">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" class="btn btn-danger removeRow">Remove</button>
                    </div>
                </div>
                <div class="row mt-2">
                </div>
            </div>`;

            $("#tripDetailsContainer").append(row);
            index++;
            // <div class="col-md-3">
            //             <label>Advance</label>
            //             <input type="number" name="trip_details[${index}][advance]" class="form-control">
            //         </div>
        });

        // document.addEventListener('input', function(e) {
        //     const row = e.target.closest('.trip-detail');
        //     if (!row) return;

        //     const totalBagsInput        = row.querySelector('.total-bags');
        //     const labourRateInput       = row.querySelector('.rate');
        //     const noOfLabourInput       = row.querySelector('.no_of_labour');
        //     const weeklyLabourInput     = row.querySelector('.weekly-labour');
        //     const balochLabourRateInput = row.querySelector('.baloch-labour-rate');
        //     const balochLabourInput     = row.querySelector('.baloch-labour');

        //     const totalBags        = totalBagsInput ? parseFloat(totalBagsInput.value) : null;
        //     const labourRate       = labourRateInput ? parseFloat(labourRateInput.value) : null;
        //     const noOfLabour       = noOfLabourInput ? parseFloat(noOfLabourInput.value) : null;
        //     const weeklyLabour     = weeklyLabourInput ? parseFloat(weeklyLabourInput.value) : null;
        //     const balochLabourRate = balochLabourRateInput ? parseFloat(balochLabourRateInput.value) : null;


        //     if(Number.isFinite(totalBags) && Number.isFinite(balochLabourRate)) {
        //         balochLabourInput.value = (totalBags * balochLabourRate);
        //     }

        //     // Only calculate if all required inputs are finite numbers
        //     if (Number.isFinite(totalBags) && Number.isFinite(labourRate) && Number.isFinite(noOfLabour)) {
        //         if (weeklyLabourInput) {
        //             weeklyLabourInput.value = (totalBags * labourRate * noOfLabour).toFixed(1);
        //         }
        //     }

        //     else if (Number.isFinite(totalBags) && Number.isFinite(noOfLabour) && Number.isFinite(weeklyLabour)) {
        //         if (labourRateInput) {
        //             labourRateInput.value = (weeklyLabour / (totalBags * noOfLabour)).toFixed(1);
        //         }
        //     }

        //     else if (Number.isFinite(labourRate) && Number.isFinite(noOfLabour) && Number.isFinite(weeklyLabour)) {
        //         if (totalBagsInput) {
        //             totalBagsInput.value = (weeklyLabour / (labourRate * noOfLabour)).toFixed(1);
        //         }
        //     }

        //     else if (Number.isFinite(totalBags) && Number.isFinite(labourRate) && Number.isFinite(weeklyLabour)) {
        //         if (noOfLabourInput) {
        //             noOfLabourInput.value = (weeklyLabour / (totalBags * labourRate)).toFixed(1);
        //         }
        //     }
        // });

        document.addEventListener('input', function(e) {
            const row = e.target.closest('.trip-detail');
            if (!row) return;

            const totalBagsInput        = row.querySelector('.total-bags');
            const labourRateInput       = row.querySelector('.rate');
            const noOfLabourInput       = row.querySelector('.no_of_labour');
            const weeklyLabourInput     = row.querySelector('.weekly-labour');
            const balochLabourRateInput = row.querySelector('.baloch-labour-rate');
            const balochLabourInput     = row.querySelector('.baloch-labour');

            const totalBags        = parseFloat(totalBagsInput?.value) || null;
            const labourRate       = parseFloat(labourRateInput?.value) || null;
            const noOfLabour       = parseFloat(noOfLabourInput?.value) || null;
            const weeklyLabour     = parseFloat(weeklyLabourInput?.value) || null;
            const balochLabourRate = parseFloat(balochLabourRateInput?.value) || null;

            const active = e.target; // jis field me user likh raha hai

            //  BALUCH LABOUR calculation
            if (Number.isFinite(totalBags) && Number.isFinite(balochLabourRate) && active !== balochLabourInput) {
                balochLabourInput.value = (totalBags * balochLabourRate).toFixed(1);
            }

            //  WEEKLY LABOUR calculation
            if (
                Number.isFinite(totalBags) && 
                Number.isFinite(labourRate) && 
                Number.isFinite(noOfLabour) && 
                active !== weeklyLabourInput
            ) {
                weeklyLabourInput.value = (totalBags * labourRate * noOfLabour).toFixed(1);
            }

            // 👇 LABOUR RATE calculation
            else if (
                Number.isFinite(totalBags) && 
                Number.isFinite(noOfLabour) && 
                Number.isFinite(weeklyLabour) && 
                active !== labourRateInput
            ) {
                labourRateInput.value = (weeklyLabour / (totalBags * noOfLabour)).toFixed(1);
            }

            // 👇 TOTAL BAGS calculation
            else if (
                Number.isFinite(labourRate) && 
                Number.isFinite(noOfLabour) && 
                Number.isFinite(weeklyLabour) && 
                active !== totalBagsInput
            ) {
                totalBagsInput.value = (weeklyLabour / (labourRate * noOfLabour)).toFixed(1);
            }

            // 👇 NO. OF LABOUR calculation
            else if (
                Number.isFinite(totalBags) && 
                Number.isFinite(labourRate) && 
                Number.isFinite(weeklyLabour) && 
                active !== noOfLabourInput
            ) {
                noOfLabourInput.value = (weeklyLabour / (totalBags * labourRate)).toFixed(1);
            }
        });

        $(document).on("change", "#trip_type", function () {
            const tripType = $(this).val();

            if (tripType === "Feed Sell") {
                $(".baloch-labour-field").show();
            } else {
                $(".baloch-labour-field").hide();
            }
        });

        // $(document).on('change keyup', '.workers, .total-bags, .labour-rate', function () {
            
        //     let row = $(this).closest('.trip-row');
        //     console.log(row);
            
        //     let workers = parseInt(row.find('.workers').val()) || 0;
        //     let bags = parseFloat(row.find('.total-bags').val()) || 0;
        //     let rate = parseFloat(row.find('.labour-rate').val()) || 0;

        //     let weeklyLabourInput = row.find('.weekly-labour');

        //     // Rule 1: Editable only if workers = 1
        //     if (workers === 1) {
        //         weeklyLabourInput.prop('readonly', false);
        //     } else {
        //         weeklyLabourInput.prop('readonly', true);
        //         // Rule 2: Auto calculate (bags * rate)
        //         let total = bags * rate;
        //         weeklyLabourInput.val(total.toFixed(2));
        //     }
        // });

        $(document).on("click", ".removeRow", function () {
            $(this).closest(".trip-detail").remove();
        });

        $(document).on("click", ".removeTripPaymentRow", function () {
            $(this).closest("tr").remove();
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
                        $('#addExpenseRow').show();
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

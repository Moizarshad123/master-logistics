@extends('admin.layouts.app')
@section('title', 'Add Loan')

@section('css')
<!-- In head -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')

<form method="POST" action="{{ route('admin.loans.store') }}">
@csrf

<div class="row">
    <div class="col-md-6">
        <label>Driver</label>
        <select name="driver_id" id="driver_id" class="form-select select2" required>
            <option value="">Select Driver</option>
            @foreach($drivers as $driver)
                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label>Driver Salary</label>
        <input type="text" id="salary" readonly class="form-control">
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-6">
        <label>Tenure From</label>
        <input type="month" id="tenure_from" name="tenure_from" class="form-control" required>
    </div>
    <div class="col-md-6">
        <label>Tenure To</label>
        <input type="month" id="tenure_to" name="tenure_to" class="form-control" required>
    </div>
</div>

<div class="row mt-2">
    <div class="col-md-4">
        <label>Loan Amount</label>
        <input type="number" id="loan_amount" name="amount" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label>Total Months</label>
        <input type="text" id="total_months" name="total_months" class="form-control" readonly>
    </div>
    <div class="col-md-4">
        <label>Monthly Installment</label>
        <input type="text" name="monthly_installment" id="monthly_installment" readonly class="form-control">
    </div>
</div>

<button class="btn btn-success mt-3">Create Loan</button>
</form>

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
$(document).on('change', '#driver_id', function () {
    let driverId = $(this).val();
    if (!driverId) {
        $('#salary').val('');
        return;
    }

    $.ajax({
        url: `/admin/drivers/${driverId}/salary`,
        type: 'GET',
        success: function (res) {
            $('#salary').val(res.salary);
        },
        error: function () {
            alert('Salary fetch failed');
        }
    });
});
</script>


<script>


function calculateInstallment() {
    let from   = document.getElementById('tenure_from').value;
    let to     = document.getElementById('tenure_to').value;
    let amount = document.getElementById('loan_amount').value;

    if (!from || !to || !amount) return;

    let start = new Date(from + "-01");
    let end   = new Date(to + "-01");

    let months = (end.getFullYear() - start.getFullYear()) * 12
               + (end.getMonth() - start.getMonth()) + 1;

    if (months <= 0) return;

    let installment = (amount / months).toFixed(2);
    document.getElementById('monthly_installment').value = installment;
    document.getElementById('total_months').value        = months;
}

document.getElementById('tenure_from').addEventListener('change', calculateInstallment);
document.getElementById('tenure_to').addEventListener('change', calculateInstallment);
document.getElementById('loan_amount').addEventListener('input', calculateInstallment);
</script>
@endsection

@extends('admin.layouts.app')
@section('title', 'Add Loan')

@section('css')
<!-- In head -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<h2>Advance Salary</h2>
<form method="POST" action="{{ route('admin.advance-salaries.store') }}">
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
            <label>Advance Month</label>
            <input type="month" name="month" class="form-control" required>
        </div>
        <div class="col-md-6">
            <label>Advance Amount</label>
            <input type="number" min="1" id="amount" name="amount" class="form-control" required>
        </div>
    </div>

    <button class="btn btn-success mt-3">Create</button>
</form>

@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
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
            url: "{{ url('admin/drivers') }}/" + driverId + "/salary",
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
@endsection

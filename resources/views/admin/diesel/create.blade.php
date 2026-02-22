@extends('admin.layouts.app')
@section('title', 'Add Diesel')

@section("css")

 <style>
    .row {
        margin-bottom: 15px;
    }
</style>    
@endsection

@section('content')
    <div class="container">
        <h2>Add Diesel</h2>
        <form action="{{ route('admin.diesel.store') }}" method="POST">
            @csrf

            <div class="row">

                <div class="col-md-4">
                    <label>Vehicle<span style="color: red">*</span></label>
                    <select name="vehicle_id" id="vehicle_id" class="form-select select2" required>
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_no }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Date<span style="color: red">*</span></label>
                    <input type="date" name="date" class="form-control" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label>Time</label>
                    <input type="time" name="time" class="form-control" value="{{ date('H:i:s') }}">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label>Type<span style="color: red">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="Diesel">Diesel</option>
                        <option value="Petrol">Petrol</option>
                        <option value="Mobil oil">Mobil oil</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Trip</label>
                    <select name="trip_id" id="trip_id" class="form-select select2">
                        <option value="">Select Trip</option>
                        @foreach($trips as $trip)
                            <option value="{{ $trip->id }}">{{ $trip->id }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Source<span style="color: red">*</span></label>
                    <select name="source" class="form-select" required>
                        <option value="">Select Source</option>
                        <option value="Master Sweetner">Master Sweetner</option>
                        <option value="PSO Pump Karachi">PSO Pump Karachi</option>
                        <option value="From Outside">From Outside</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <label>Litres<span style="color: red">*</span></label>
                    <input type="number" step="0.01" name="litres" id="litres" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Per Liter Amount<span style="color: red">*</span></label>
                    <input type="number" step="0.01" name="per_litre_amount" id="per_litre_amount" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Total Amount</label>
                    <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" readonly>
                </div>
            </div>
            <button class="btn btn-primary mt-3">Save</button>
        </form>
    </div>
@endsection

@section('js')
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
        function calculateTotal() {
            let litres = parseFloat($('#litres').val()) || 0;
            let price = parseFloat($('#per_litre_amount').val()) || 0;

            let total = litres * price;

            $('#total_amount').val(total.toFixed(1));
        }

        $('#litres, #per_litre_amount').on('input', function() {
            calculateTotal();
        });
    </script>
@endsection
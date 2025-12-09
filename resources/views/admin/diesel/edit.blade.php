@extends('admin.layouts.app')
@section('title', 'Edit Diesel')

@section("css")
 <style>
    .row {
        margin-bottom: 15px;
    }
</style>    
@endsection

@section('content')
<div class="container">
    <h2>Edit Diesel</h2>

   <form action="{{ route('admin.diesel.update', $diesel->id) }}" method="POST">
        @csrf
        @method("PUT")
        <div class="row">
            <div class="col-md-4">
                <label>Type<span style="color: red">*</span></label>
                <select name="type" class="form-control" required>
                    <option value="Diesel" {{ $diesel->type == "Diesel" ? "selected" : ""}}>Diesel</option>
                    <option value="Petrol" {{ $diesel->type == "Petrol" ? "selected" : ""}}>Petrol</option>
                    <option value="Mobil oil" {{ $diesel->type == "Mobil oil" ? "selected" : ""}}>Mobil oil</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Date<span style="color: red">*</span></label>
                <input type="date" name="date" class="form-control" required value="{{ $diesel->date}}">
            </div>
            <div class="col-md-4">
                <label>Time</label>
                <input type="time" name="time" class="form-control" value="{{ $diesel->time}}">
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <label>Litres<span style="color: red">*</span></label>
                <input type="number" step="0.01" name="litres" id="litres" class="form-control" required value="{{ $diesel->litres}}">
            </div>

            <div class="col-md-4">
                <label>Per Liter Amount<span style="color: red">*</span></label>
                <input type="number" step="0.01" name="per_litre_amount" id="per_litre_amount" class="form-control" required value="{{ $diesel->per_litre_amount}}">
            </div>

            <div class="col-md-4">
                <label>Total Amount</label>
                <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control" readonly value="{{ $diesel->total_amount}}">
            </div>
        </div>
        <button class="btn btn-primary mt-3">Save</button>
    </form>

</div>
@endsection
@section('js')
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
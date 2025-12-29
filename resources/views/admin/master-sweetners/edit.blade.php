@extends('admin.layouts.app')
@section('title','Update Master Sweetner')

@section('content')

<form method="POST" enctype="multipart/form-data" action="{{ route('admin.master-sweetners.update',$masterSweetner->id) }}">
    @csrf
    @method('PUT')

    <div class="row">
        <div class="col-md-6">
            <label>Supplier *</label>
            <select name="supplier_id" class="form-select" required>
                <option value="">Select Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"
                        {{ isset($masterSweetner) && $masterSweetner->supplier_id == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-6">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="{{ $masterSweetner->date ?? '' }}">
        </div>
        <div class="col-md-4">
            <label>Fuel Type<span style="color: red">*</span></label>
            <select name="fuel_type" class="form-select" required>
                <option value="Diesel"  {{ $masterSweetner->fuel_type == "Diesel" ? 'selected' : '' }}>Diesel</option>
                <option value="Petrol"  {{ $masterSweetner->fuel_type == "Petrol" ? 'selected' : '' }}>Petrol</option>
            </select>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-4">
            <label>Total Litres</label>
            <input type="number" step="0.01" name="total_litres" id="total_litres" class="form-control"
                   value="{{ $masterSweetner->total_litres ?? '' }}">
        </div>
    
        <div class="col-md-4">
            <label>Per Litre Price</label>
            <input type="number" step="0.01" name="per_litre_price" id="per_litre_price" class="form-control"
                   value="{{ $masterSweetner->per_litre_price ?? '' }}">
        </div>
        <div class="col-md-4">
            <label>Total Amount</label>
            <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control"
                   value="{{ $masterSweetner->total_amount ?? '' }}" readonly>
        </div>

    </div>

    <div class="row mt-2">
        <div class="col-md-6">
            <label>Receiving Receipt</label>
            <input type="file" name="receiving_receipt" class="form-control">
        </div>

        <div class="col-md-6">
            <label>Delivery Challan</label>
            <input type="file" name="delivery_challan" class="form-control">
        </div>
    </div>

    <button class="btn btn-success mt-3">Save</button>
    <a href="{{ route('admin.master-sweetners.index') }}" class="btn btn-secondary mt-3">Back</a>
</form>

@endsection


@section('js')
<script>
    function calculateTotal() {
        let litres = parseFloat($('#total_litres').val()) || 0;
        let price  = parseFloat($('#per_litre_price').val()) || 0;

        let total = litres * price;
        $('#total_amount').val(total.toFixed(2));
    }

    $(document).on('keyup change', '#total_litres, #per_litre_price', function () {
        calculateTotal();
    });

    // Edit page load par bhi calculate ho
    $(document).ready(function () {
        calculateTotal();
    });
</script>
@endsection


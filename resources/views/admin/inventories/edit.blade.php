@extends('admin.layouts.app')

@section('title', 'Edit Inventory Item')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">Edit Item</h4>
            <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.inventories.update', $inventory->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-4 mb-3">
                        <label>Item Name <span class="text-danger">*</span></label>
                        <select name="item_id" id="item_name" class="form-select" required>
                            <option value="">Select Item</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}"
                                        data-make="{{ $item->make }}"
                                        data-model="{{ $item->model }}"
                                        data-price="{{ $item->price }}"
                                        {{ old('item_id', $inventory->item_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Make</label>
                        <input type="text" name="make" readonly class="form-control"
                               value="{{ old('make', $inventory->item?->make) }}" placeholder="Auto-filled">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Model</label>
                        <input type="text" name="model" readonly class="form-control"
                               value="{{ old('model', $inventory->item?->model) }}" placeholder="Auto-filled">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Unit Price (Rs.) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="unit_price" id="unit_price"
                               class="form-control @error('unit_price') is-invalid @enderror"
                               value="{{ old('unit_price', $inventory->unit_price) }}" placeholder="0.00" required>
                        @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="qty" id="qty"
                               class="form-control @error('qty') is-invalid @enderror"
                               value="{{ old('qty', $inventory->qty) }}" min="0" required>
                        @error('qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Total Price (Rs.) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="total_price" id="total_price"
                               class="form-control @error('total_price') is-invalid @enderror"
                               value="{{ old('total_price', $inventory->total_price) }}" placeholder="0.00" required>
                        @error('total_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date"
                               class="form-control @error('purchase_date') is-invalid @enderror"
                               value="{{ old('purchase_date', $inventory->purchase_date->format('Y-m-d')) }}" required>
                        @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Vendor</label>
                        <input type="text" name="vendor"
                               class="form-control @error('vendor') is-invalid @enderror"
                               value="{{ old('vendor', $inventory->vendor) }}" placeholder="Vendor name">
                        @error('vendor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Invoice No</label>
                        <input type="text" name="invoice_no"
                               class="form-control @error('invoice_no') is-invalid @enderror"
                               value="{{ old('invoice_no', $inventory->invoice_no) }}" placeholder="Invoice number">
                        @error('invoice_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-sync-alt"></i> Update Item
                </button>
                <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>

@endsection

@section('js')
<script>
    $(document).ready(function () {

        $('#item_name').select2({
            placeholder: "Select Item",
            allowClear: true
        });

        // Auto-calculate total price
        $('#qty, #unit_price').on('input', function () {
            var qty        = parseFloat($('#qty').val()) || 0;
            var unit_price = parseFloat($('#unit_price').val()) || 0;
            $('#total_price').val((qty * unit_price).toFixed(2));
        });

        // Auto-fill make, model, unit_price on item change
        $('#item_name').on('change', function () {
            var selected = $(this).find(':selected');

            $('input[name="make"]').val(selected.data('make') || '');
            $('input[name="model"]').val(selected.data('model') || '');
            $('#unit_price').val(selected.data('price') || '');

            // Recalculate total
            var qty        = parseFloat($('#qty').val()) || 0;
            var unit_price = parseFloat(selected.data('price')) || 0;
            $('#total_price').val((qty * unit_price).toFixed(2));

            if (!$(this).val()) {
                $('input[name="make"]').val('');
                $('input[name="model"]').val('');
                $('#unit_price').val('');
                $('#total_price').val('');
            }
        });
    });
</script>
@endsection
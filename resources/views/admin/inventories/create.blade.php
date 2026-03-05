{{-- resources/views/admin/inventories/create.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Add Purchase')

@section('content')
    <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">Add New Purchase</h4>
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
        
            <form action="{{ route('admin.inventories.store') }}" method="POST">
                @csrf
        
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Item Name <span class="text-danger">*</span></label>
                        <select name="item_id" id="item_name"  class="form-select" required>
                            <option value="">Select Item</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}"
                                        data-make="{{ $item->make }}"
                                        data-model="{{ $item->model }}"
                                        data-price="{{ $item->price }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
        
                    <div class="col-md-3 mb-3">
                        <label>Make</label>
                        <input type="text" name="make" readonly class="form-control @error('make') is-invalid @enderror"
                               value="{{ old('make') }}" placeholder="e.g. Samsung, Dell">
                        @error('make') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
        
                    <div class="col-md-3 mb-3">
                        <label>Model</label>
                        <input type="text" name="model" readonly class="form-control @error('model') is-invalid @enderror"
                               value="{{ old('model') }}" placeholder="e.g. Galaxy S23">
                        @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Unit Price (Rs.) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="unit_price" readonly class="form-control @error('unit_price') is-invalid @enderror"
                                value="{{ old('unit_price') }}" placeholder="0.00" required>
                        @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
        
                    
                    <div class="col-md-4 mb-3">
                        <label>Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror"
                        value="{{ old('purchase_date') ?? date('Y-m-d') }}" required>
                        @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="qty" class="form-control @error('qty') is-invalid @enderror"
                               value="{{ old('qty') }}" min="1" required>
                        @error('qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Total Price (Rs.) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price') }}" placeholder="0.00" required>
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    
        
                </div>
        
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Item
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

        $('input[name="qty"], input[name="unit_price"]').on('input', function () {
            var qty        = parseFloat($('input[name="qty"]').val()) || 0;
            var unit_price = parseFloat($('input[name="unit_price"]').val()) || 0;

            $('input[name="price"]').val((qty * unit_price).toFixed(2));
        });

        $('#item_name').on('change', function () {
            var selected = $(this).find(':selected');

            $('input[name="make"]').val(selected.data('make') || '');
            $('input[name="model"]').val(selected.data('model') || '');
            $('input[name="unit_price"]').val(selected.data('price') || '');

            // Clear on reset
            if (!$(this).val()) {
                $('input[name="make"]').val('');
                $('input[name="model"]').val('');
                $('input[name="unit_price"]').val('');
            }
        });
    });
</script>
@endsection
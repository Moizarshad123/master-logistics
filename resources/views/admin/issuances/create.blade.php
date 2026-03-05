{{-- resources/views/admin/issuances/create.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Issue Item')

@section('content')

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10"><h3>Issue Item</h3></div>
            <div class="col-md-2">
                <a href="{{ route('admin.issuances.index') }}" class="btn btn-sm btn-secondary">Back</a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.issuances.store') }}" method="POST">
                @csrf

                <div class="row">

                    {{-- Vehicle Dropdown --}}
                    <div class="col-md-6 mb-3">
                        <label>Vehicle <span class="text-danger">*</span></label>
                        <select name="vehicle_id" id="vehicle" class="form-control @error('vehicle_id') is-invalid @enderror" required>
                            <option value="">-- Select Vehicle --</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}"
                                    {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->vehicle_no }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Item Dropdown — grouped by item_name --}}
                    <div class="col-md-6 mb-3">
                        <label>Item Name <span class="text-danger">*</span></label>
                        <select name="inventory_id" id="inventorySelect"
                                class="form-control @error('inventory_id') is-invalid @enderror" required>
                            <option value="">-- Select Item --</option>
                            @foreach($inventories as $itemName => $items)
                                <optgroup label="{{ $itemName }}">
                                    @foreach($items as $inv)
                                        <option value="{{ $inv->id }}"
                                                data-remaining="{{ $inv->remaining_qty }}"
                                            {{ old('inventory_id') == $inv->id ? 'selected' : '' }}>
                                            {{ $inv->item_name }} (Available: {{ $inv->remaining_qty }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('inventory_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

                        {{-- Live available qty badge --}}
                        <small id="availableQtyBadge" class="text-muted mt-1 d-block"></small>
                    </div>

                    {{-- Qty --}}
                    <div class="col-md-6 mb-3">
                        <label>Qty to Issue <span class="text-danger">*</span></label>
                        <input type="number" name="qty" id="qtyInput"
                               class="form-control @error('qty') is-invalid @enderror"
                               value="{{ old('qty') }}" min="1" required>
                        @error('qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small id="qtyWarning" class="text-danger d-none">Qty exceeds available stock!</small>
                    </div>

                    {{-- Issue Date --}}
                    <div class="col-md-6 mb-3">
                        <label>Issue Date <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date"
                               class="form-control @error('issue_date') is-invalid @enderror"
                               value="{{ old('issue_date', date('Y-m-d')) }}" required>
                        @error('issue_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>

                <button type="submit" class="btn btn-success" id="submitBtn">
                    <i class="fas fa-check"></i> Issue Item
                </button>
                <a href="{{ route('admin.issuances.index') }}" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>

@endsection

@section('js')
<script>
    $(document).ready(function () {

        $('#vehicle').select2({
            placeholder: "Select Expenses",
            allowClear: true
        });


        $('#inventorySelect').select2({
            placeholder: "Select Expenses",
            allowClear: true
        });        
        var maxQty = 0;

        // Jab item change ho
        $('#inventorySelect').on('change', function () {
            var selected = $(this).find('option:selected');
            maxQty = parseInt(selected.data('remaining')) || 0;

            if ($(this).val()) {
                $('#availableQtyBadge').html(
                    '<span class="badge badge-info">Available Stock: <strong>' + maxQty + '</strong></span>'
                );
            } else {
                $('#availableQtyBadge').html('');
            }

            checkQty();
        });

        // Jab qty type ho
        $('#qtyInput').on('input', function () {
            checkQty();
        });

        function checkQty() {
            var enteredQty = parseInt($('#qtyInput').val()) || 0;
            if (maxQty > 0 && enteredQty > maxQty) {
                $('#qtyWarning').removeClass('d-none');
                $('#submitBtn').prop('disabled', true);
            } else {
                $('#qtyWarning').addClass('d-none');
                $('#submitBtn').prop('disabled', false);
            }
        }

        // On page load agar old value hai
        if ($('#inventorySelect').val()) {
            $('#inventorySelect').trigger('change');
        }

    });
</script>
@endsection
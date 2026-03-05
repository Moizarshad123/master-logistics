{{-- resources/views/admin/issuances/edit.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Edit Issuance')

@section('content')

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10"><h3>Edit Issuance</h3></div>
            <div class="col-md-2">
                <a href="{{ route('admin.issuances.index') }}" class="btn btn-sm btn-secondary">Back</a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.issuances.update', $issuance->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    {{-- Vehicle --}}
                    <div class="col-md-6 mb-3">
                        <label>Vehicle <span class="text-danger">*</span></label>
                        <select name="vehicle_id" class="form-control @error('vehicle_id') is-invalid @enderror" required>
                            <option value="">-- Select Vehicle --</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}"
                                    {{ old('vehicle_id', $issuance->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->vehicle_no }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Item — grouped --}}
                    <div class="col-md-6 mb-3">
                        <label>Item Name <span class="text-danger">*</span></label>
                        <select name="inventory_id" id="inventorySelect"
                                class="form-control @error('inventory_id') is-invalid @enderror" required>
                            <option value="">-- Select Item --</option>
                            @foreach($inventories as $itemName => $items)
                                <optgroup label="{{ $itemName }}">
                                    @foreach($items as $inv)
                                        {{--
                                            Edit mein: agar same item hai to available = remaining + current issued qty
                                            Yeh backend par check hoga, front-end pe sirf display k liye
                                        --}}
                                        @php
                                            $displayQty = ($inv->id == $issuance->inventory_id)
                                                ? $inv->remaining_qty + $issuance->qty
                                                : $inv->remaining_qty;
                                        @endphp
                                        <option value="{{ $inv->id }}"
                                                data-remaining="{{ $displayQty }}"
                                            {{ old('inventory_id', $issuance->inventory_id) == $inv->id ? 'selected' : '' }}>
                                            {{ $inv->item_name }} (Available: {{ $displayQty }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        @error('inventory_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small id="availableQtyBadge" class="text-muted mt-1 d-block"></small>
                    </div>

                    {{-- Qty --}}
                    <div class="col-md-6 mb-3">
                        <label>Qty to Issue <span class="text-danger">*</span></label>
                        <input type="number" name="qty" id="qtyInput"
                               class="form-control @error('qty') is-invalid @enderror"
                               value="{{ old('qty', $issuance->qty) }}" min="1" required>
                        @error('qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small id="qtyWarning" class="text-danger d-none">Qty exceeds available stock!</small>
                    </div>

                    {{-- Issue Date --}}
                    <div class="col-md-6 mb-3">
                        <label>Issue Date <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date"
                               class="form-control @error('issue_date') is-invalid @enderror"
                               value="{{ old('issue_date', $issuance->issue_date->format('Y-m-d')) }}" required>
                        @error('issue_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>

                <button type="submit" class="btn btn-warning" id="submitBtn">
                    <i class="fas fa-sync-alt"></i> Update Issuance
                </button>
                <a href="{{ route('admin.issuances.index') }}" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>

@endsection

@section('js')
<script>
    $(document).ready(function () {

        var maxQty = 0;

        function updateBadge() {
            var selected = $('#inventorySelect').find('option:selected');
            maxQty = parseInt(selected.data('remaining')) || 0;

            if ($('#inventorySelect').val()) {
                $('#availableQtyBadge').html(
                    '<span class="badge badge-info">Available Stock: <strong>' + maxQty + '</strong></span>'
                );
            } else {
                $('#availableQtyBadge').html('');
            }
            checkQty();
        }

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

        $('#inventorySelect').on('change', updateBadge);
        $('#qtyInput').on('input', checkQty);

        // Init on load
        updateBadge();
    });
</script>
@endsection
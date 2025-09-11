
<div class="row" style="margin-bottom:15px">
    <div class="col-md-3">
        <div class="form-group">
            <label>Vehicle No<span style="color: red">*</span></label>
            <input type="text" name="vehicle_no" class="form-control" value="{{ old('vehicle_no', $vehicle->vehicle_no ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Vehicle Type<span style="color: red">*</span></label>
            <select name="vehicle_type" class="form-select" required>
                <option value="">Select Wheeler Type</option>
                @foreach ($wheelers as $item)
                    <option value="{{ $item->id }}" {{ old('vehicle_type', $vehicle->vehicle_type ?? '') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                @endforeach
            </select>
            {{-- <input type="text"   value="{{ old('vehicle_type', $vehicle->vehicle_type ?? '') }}" > --}}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Chachis No</label>
            <input type="text" name="chachis_no" class="form-control" value="{{ old('chachis_no', $vehicle->chachis_no ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Engine No</label>
            <input type="text" name="engine_no" class="form-control" value="{{ old('engine_no', $vehicle->engine_no ?? '') }}" required>
        </div>
    </div>
</div>

<div class="row" style="margin-bottom:15px">
    <div class="col-md-6">
        <div class="form-group">
            <label for="expense_types">Expenses<span style="color: red">*</span></label>
            <select name="expense_types[]" id="expense_types" class="form-select select2" multiple="multiple">
                @php
                    $selectedExpenses = old(
                        'expense_types',
                        isset($vehicle) ? $vehicle->expenseTypes->pluck('id')->toArray() : []
                    );
                @endphp
                @foreach($expenses as $expense)
                    <option value="{{ $expense->id }}" 
                        {{ in_array($expense->id, $selectedExpenses) ? 'selected' : '' }}>
                        {{ $expense->name }}
                    </option>
                @endforeach

            </select>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Make</label>
            <input type="text" name="make" class="form-control" value="{{ old('make', $vehicle->make ?? '') }}" required>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label>Model</label>
            <input type="text" name="model" class="form-control" value="{{ old('model', $vehicle->model ?? '') }}" required>
        </div>
    </div>
</div>

<div class="row" style="margin-bottom:15px">
    <div class="col-md-6">
        <div class="form-group">
            <label>Vehicle Image</label>
            <input type="file" name="image" class="form-control image-input" data-preview="driver_image_preview">
        </div>
    </div>
     <div class="col-md-6">
        <img id="driver_image_preview" 
            src="{{ isset($vehicle) && $vehicle->image ? $vehicle->image : ''}}" 
            alt="Driver" class="mt-2" height="150"
            style="{{ isset($vehicle) && $vehicle->image ? '' : 'display:none;' }}">
    </div>
</div>


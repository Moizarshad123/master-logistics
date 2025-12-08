
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
            <label>Route Permit Sindh</label>
            <input type="file" name="route_permit_sindh" class="form-control">
        </div>
    </div>
     <div class="col-md-6">
        <label>Route Permit Sindh Expiry Date</label>
        <input type="date" name="route_permit_sindh_expiry" class="form-control" value="{{ old('route_permit_sindh_expiry', $vehicle->route_permit_sindh_expiry ?? '') }}">
    </div>
</div>

<div class="row" style="margin-bottom:15px">
    <div class="col-md-6">
        <div class="form-group">
            <label>Route Permit Punjab</label>
            <input type="file" name="route_permit_punjab" class="form-control">
        </div>
    </div>
     <div class="col-md-6">
        <label>Route Permit Punjab Expiry Date</label>
        <input type="date" name="route_permit_punjab_expiry" class="form-control" value="{{ old('route_permit_punjab_expiry', $vehicle->route_permit_punjab_expiry ?? '') }}">
    </div>
</div>

<div class="row" style="margin-bottom:15px">
    <div class="col-md-6">
        <div class="form-group">
            <label>Fitness Certificate</label>
            <input type="file" name="fitness_certificate" class="form-control">
        </div>
    </div>
     <div class="col-md-6">
        <label>Fitness Certificate Expiry Date</label>
        <input type="date" name="fitness_certificate_expiry" class="form-control" value="{{ old('fitness_certificate_expiry', $vehicle->fitness_certificate_expiry ?? '') }}">
    </div>
</div>

<div class="row" style="margin-bottom:15px">
    <div class="col-md-6">
        <div class="form-group">
            <label>Insurance Certificate</label>
            <input type="file" name="insurance_certificate" class="form-control">
        </div>
    </div>
     <div class="col-md-6">
        <label>Insurance Certificate Expiry Date</label>
        <input type="date" name="insurance_certificate_expiry" class="form-control" value="{{ old('insurance_certificate_expiry', $vehicle->insurance_certificate_expiry ?? '') }}">
    </div>
</div>

<div class="row" style="margin-bottom:15px">
    <div class="col-md-6">
        <div class="form-group">
            <label>Tax Token</label>
            <input type="file" name="tax_token" class="form-control">
        </div>
    </div>
     <div class="col-md-6">
        <label>Tax Token Expiry Date</label>
        <input type="date" name="tax_token_expiry" class="form-control" value="{{ old('tax_token_expiry', $vehicle->tax_token_expiry ?? '') }}">
    </div>
</div>

<div class="row" style="margin-bottom:15px">
    <div class="col-md-6">
        <div class="form-group">
            <label>Vehicle Image</label>
            <input type="file" name="image" class="form-control image-input" data-preview="driver_image_preview">
        </div>
    </div>
     {{-- <div class="col-md-6">
        <img id="driver_image_preview" 
            src="{{ isset($vehicle) && $vehicle->image ? $vehicle->image : ''}}" 
            alt="Driver" class="mt-2" height="150"
            style="{{ isset($vehicle) && $vehicle->image ? '' : 'display:none;' }}">
    </div> --}}
    <div class="col-md-6">
        <div class="form-group">
            <label>Vehicle File</label>
            <input type="file" name="vehicle_file" class="form-control">
        </div>
    </div>
</div>

<div class="row" style="margin-bottom:15px">
    
</div>





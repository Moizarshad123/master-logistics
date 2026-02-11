<div class="row">

      <div class="col-md-3">
        <div class="mb-3">
            <label for="">Vehicle</label>
            <select name="vehicle_id" id="vehicle_id" class="form-select">
                @if($driver == null)
                <option value="">Select Vehicle</option>
                @endif
                @foreach ($vehicles as $vehicle)
                    @if($driver != null)
                        <option value="{{ $vehicle->id }}" {{ $vehicle->id == $driver->vehicle_id ? "selected" : '' }}>{{ $vehicle->vehicle_no ?? ''}}</option>
                    @else
                        <option value="{{ $vehicle->id }}">{{ $vehicle->vehicle_no ?? ''}}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
    <div class="col-md-3">
        <label for="name" class="form-label">Driver Name<span style="color: red">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $driver->name ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label for="phone" class="form-label">Phone<span style="color: red">*</span></label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $driver->phone ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-3">
        <label for="phone" class="form-label">Salary<span style="color: red">*</span></label>
        <input type="number" min="1" name="salary" id="salary" value="{{ old('salary', $driver->salary ?? '') }}" class="form-control" required>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <label for="">CNIC Expiry Date</label>
        <input type="date" class="form-control" name="cnic_expiry_date" value="{{ old('cnic_expiry_date', $driver->cnic_expiry_date ?? '') }}">
    </div>
    <div class="col-md-3">
        <label for="">License Expiry Date</label>
        <input type="date" class="form-control" name="license_expiry_date" value="{{ old('license_expiry_date', $driver->license_expiry_date ?? '') }}">
    </div>
    <div class="col-md-3">
        <label for="">Driver Employment Status</label>
        @if($driver != null)
            <select name="status" class="form-select">
                <option value="left" {{ $driver->status == "left" ? "selected" : ""}}>Left</option>
                <option value="resign" {{ $driver->status == "resign" ? "selected" : ""}}>Resign</option>
                <option value="terminate" {{ $driver->status == "terminate" ? "selected" : ""}}>Terminate</option>
                <option value="active" {{ $driver->status == "active" ? "selected" : ""}}>Active</option>
            </select>
        @else 
            <select name="status" class="form-select">
                <option value="left" >Left</option>
                <option value="resign">Resign</option>
                <option value="terminate">Terminate</option>
                <option value="active">Active</option>
            </select>
        @endif
    </div>
    <div class="col-md-3">
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea name="address" id="address" class="form-control" required>{{ old('address', $driver->address ?? '') }}</textarea>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <label for="cnic_front" class="form-label">CNIC Front</label>
        <input type="file" name="cnic_front" class="image-input form-control" {{ isset($driver) ? '' : 'required' }} data-preview="cnic_front_preview">
    </div>
    <div class="col-md-6">
        <img id="cnic_front_preview" 
             src="{{ isset($driver) && $driver->cnic_front ? $driver->cnic_front : '' }}" 
             alt="CNIC Front" class="mt-2" height="150"
             style="{{ isset($driver) && $driver->cnic_front ? '' : 'display:none;' }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <label for="cnic_back" class="form-label">CNIC Back</label>
        <input type="file" name="cnic_back" class="form-control image-input" data-preview="cnic_back_preview" {{ isset($driver) ? '' : 'required' }}>
    </div>
    <div class="col-md-6">
        <img id="cnic_back_preview" 
            src="{{ isset($driver) && $driver->cnic_back ? $driver->cnic_back : '' }}" 
            alt="CNIC Back" class="mt-2" height="150"
            style="{{ isset($driver) && $driver->cnic_back ? '' : 'display:none;' }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <label for="driving_license_front" class="form-label">Driving License Front</label>
        <input type="file" name="driving_license_front" class="form-control image-input" data-preview="license_front_preview"  {{ isset($driver) ? '' : 'required' }}>
    </div>
    <div class="col-md-6">
        <img id="license_front_preview" 
             src="{{ isset($driver) && $driver->driving_license_front ? $driver->driving_license_front : '' }}" 
             alt="License Front" class="mt-2" height="150"
             style="{{ isset($driver) && $driver->driving_license_front ? '' : 'display:none;' }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <label for="driving_license_back" class="form-label">Driving License Back</label>
        <input type="file" name="driving_license_back" class="form-control image-input"  data-preview="license_back_preview" {{ isset($driver) ? '' : 'required' }}>
    </div>
    <div class="col-md-6">
        <img id="license_back_preview" 
            src="{{ isset($driver) && $driver->driving_license_back ? $driver->driving_license_back : '' }}" 
            alt="License Back" class="mt-2" height="150"
            style="{{ isset($driver) && $driver->driving_license_back ? '' : 'display:none;' }}">
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <label for="image" class="form-label">Driver Image</label>
        <input type="file" name="image" class="form-control image-input" data-preview="driver_image_preview" {{ isset($driver) ? '' : 'required' }}>
    </div>
    <div class="col-md-6">
        <img id="driver_image_preview" 
            src="{{ isset($driver) && $driver->image ? $driver->image : ''}}" 
            alt="Driver" class="mt-2" height="150"
            style="{{ isset($driver) && $driver->image ? '' : 'display:none;' }}">
    </div>
    
</div>
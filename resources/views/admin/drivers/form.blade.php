<div class="row">
    <div class="col-md-4">
        <label for="name" class="form-label">Driver Name<span style="color: red">*</span></label>
        <input type="text" name="name" id="name" value="{{ old('name', $driver->name ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label for="phone" class="form-label">Phone<span style="color: red">*</span></label>
        <input type="text" name="phone" id="phone" value="{{ old('phone', $driver->phone ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label for="phone" class="form-label">Salary<span style="color: red">*</span></label>
        <input type="number" min="1" name="salary" id="salary" value="{{ old('salary', $driver->salary ?? '') }}" class="form-control" required>
    </div>
</div>


<div class="mb-3">
    <label for="address" class="form-label">Address</label>
    <textarea name="address" id="address" class="form-control" required>{{ old('address', $driver->address ?? '') }}</textarea>
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
   






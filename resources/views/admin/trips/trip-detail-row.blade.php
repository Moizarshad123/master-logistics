<div class="trip-detail border rounded p-3 mb-3">
    <div class="row">
        <div class="col-md-4">
            <label>Customer<span style="color: red">*</span></label>
            <select name="trip_details[{{ $index }}][customer_id]" class="form-select" required>
                <option value="">Select Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ $customer->id == $detail->customer_id ? "selected" : ""}}>{{$customer->name}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label>Trip Type<span style="color: red">*</span></label>
            <select name="trip_details[{{ $index }}][trip_type]" class="form-select trip_type" required>
                <option value="">Select Trip Type</option>
                <option value="Commercial" {{ $detail->trip_type == "Commercial" ? "selected" : ""}}>Commercial</option>
                <option value="Purchase" {{ $detail->trip_type == "Purchase" ? "selected" : ""}}>Purchase</option>
                <option value="Feed Sell" {{ $detail->trip_type == "Feed Sell" ? "selected" : ""}}>Feed Sell</option>
                <option value="Other Sell" {{ $detail->trip_type == "Other Sell" ? "selected" : ""}}>Other Sell</option>
                <option value="Local" {{ $detail->trip_type == "Local" ? "selected" : ""}}>Local</option>
            </select>
        </div>
        <div class="col-md-4">
            <label>Start Date</label>
            <input type="date" name="trip_details[{{ $index }}][start_date]" value="{{ old("trip_details.$index.start_date", $detail->start_date ?? '') }}" class="form-control" required>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-6 from-container"></div>
        <div class="col-md-6 to-container"></div>
    </div>

    <div class="row mt-2">
        <div class="col-md-6">
            <label>From</label>
            <select class="form-select" name="trip_details[{{ $index }}][from_destination]">
                @foreach ($destinations as $item)
                    <option value="{{ $item->id }}" {{ $detail->from_destination == $item->name ? "selected" : ""}}>{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label>To</label>
            <select class="form-select" name="trip_details[{{ $index }}][to_destination]">
                @foreach ($destinations as $item)
                    <option value="{{ $item->id }}" {{ $detail->to_destination == $item->name ? "selected" : ""}}>{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="row mt-2">
        <div class="col-md-3">
            <label>Material</label>
            <input type="text" name="trip_details[{{ $index }}][material]" value="{{ old("trip_details.$index.material", $detail->material ?? '') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Material Type</label>
            <select class="form-select" name="trip_details[{{ $index }}][material_type]">
                <option value="Bags" {{ $detail->material_type == "Bags" ? "selected" : "" }}>Bags</option>
                <option value="Tainki" {{ $detail->material_type == "Tainki" ? "selected" : "" }}>Tainki</option>
                <option value="LTR" {{ $detail->material_type == "LTR" ? "selected" : "" }}>LTR</option>
            </select>
        </div>
        <div class="col-md-3 baloch-labour-field">
            <label>Baloch Labour Rate</label>
            <input type="text" name="trip_details[{{ $index }}][baloch_labour_rate]" value="{{ $detail->baloch_labour_rate ?? ''}}"  class="form-control baloch-labour-rate">
        </div>
        <div class="col-md-3 baloch-labour-field">
            <label>Baloch Labour</label>
            <input type="text" name="trip_details[{{ $index }}][baloch_labour]"  value="{{ $detail->baloch_labour ?? ''}}" class="form-control baloch-labour">
        </div>
    </div>
    

    <div class="row mt-2">
        <div class="col-md-3">
            <label>Total Bags</label>
            <input type="number" name="trip_details[{{ $index }}][total_bags]" value="{{ old("trip_details.$index.total_bags", $detail->total_bags ?? '') }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label>No Of Labour</label>
            <input type="text" name="trip_details[{{ $index }}][no_of_labour]" class="form-control no_of_labour" value="{{ $detail->no_of_labour ?? ''}}">
        </div>
        <div class="col-md-3">
            <label>Weekly Labour Rate</label>
            <input type="text" name="trip_details[{{ $index }}][rate]" class="form-control rate" value="{{ $detail->rate ?? ''}}">
        </div>
        <div class="col-md-3">
            <label>Weekly Labour</label>
            <input type="text" name="trip_details[{{ $index }}][weekly_labour]" class="form-control weekly-labour" value="{{ $detail->weekly_labour ?? ''}}">
        </div>
    </div>
    
    <div class="row mt-2">

        {{-- <div class="col-md-4">
            <label>Weekly Labour</label>
            <input type="text" name="trip_details[{{ $index }}][weekly_labour]" value="{{ old("trip_details.$index.weekly_labour", $detail->weekly_labour ?? '') }}" class="form-control weekly-labour">
        </div>

        <div class="col-md-4">
            <label>Baloch Labour</label>
            <input type="text" name="trip_details[{{ $index }}][baloch_labour]" value="{{ old("trip_details.$index.baloch_labour", $detail->baloch_labour ?? '') }}" class="form-control">
        </div> --}}
        
        <div class="col-md-4">
            <label>Rent</label>
            <input type="number" name="trip_details[{{ $index }}][rent]" value="{{ old("trip_details.$index.rent", $detail->rent ?? '') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label>Weight (In Ton)</label>
            <input type="number" step="0.01" name="trip_details[{{ $index }}][weight]" value="{{ old("trip_details.$index.weight", $detail->weight ?? '') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label>Comments</label>
            <textarea  name="trip_details[${index}][comments]" class="form-control">{{  $detail->comments ?? '' }}</textarea>
        </div>
    </div>
</div>

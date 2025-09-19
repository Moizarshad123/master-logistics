<div class="trip-detail border rounded p-3 mb-3">
    <div class="row">
        <div class="col-md-4">
            <label>Start Date</label>
            <input type="date" name="trip_details[{{ $index }}][start_date]" value="{{ old("trip_details.$index.start_date", $detail->start_date ?? '') }}" class="form-control" required>
        </div>
        {{-- <div class="col-md-3">
            <label>End Date</label>
            <input type="date" name="trip_details[{{ $index }}][end_date]" value="{{ old("trip_details.$index.end_date", $detail->end_date ?? '') }}" class="form-control">
        </div> --}}
        <div class="col-md-4">
            <label>From</label>
            <input type="text" name="trip_details[{{ $index }}][from_destination]" value="{{ old("trip_details.$index.from_destination", $detail->from_destination ?? '') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label>To</label>
            <input type="text" name="trip_details[{{ $index }}][to_destination]" value="{{ old("trip_details.$index.to_destination", $detail->to_destination ?? '') }}" class="form-control">
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label>Material</label>
            <input type="text" name="trip_details[{{ $index }}][material]" value="{{ old("trip_details.$index.material", $detail->material ?? '') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label>Total Bags</label>
            <input type="number" name="trip_details[{{ $index }}][total_bags]" value="{{ old("trip_details.$index.total_bags", $detail->total_bags ?? '') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label>Loading Labour</label>
            <input type="text" name="trip_details[{{ $index }}][loading_labour]" value="{{ old("trip_details.$index.loading_labour", $detail->loading_labour ?? '') }}" class="form-control">
        </div>
        
    </div>

    <div class="row mt-2">
        <div class="col-md-4">
            <label>Unloading Labour</label>
            <input type="text" name="trip_details[{{ $index }}][unloading_labour]" value="{{ old("trip_details.$index.unloading_labour", $detail->unloading_labour ?? '') }}" class="form-control">
        </div>
        <div class="col-md-4">
            <label>Rent</label>
            <input type="number" name="trip_details[{{ $index }}][rent]" value="{{ old("trip_details.$index.rent", $detail->rent ?? '') }}" class="form-control">
        </div>
        {{-- <div class="col-md-3">
            <label>Advance</label>
            <input type="number" name="trip_details[{{ $index }}][advance]" value="{{ old("trip_details.$index.advance", $detail->advance ?? '') }}" class="form-control">
        </div> --}}
        <div class="col-md-4">
            <label>Weight</label>
            <input type="number" step="0.01" name="trip_details[{{ $index }}][weight]" value="{{ old("trip_details.$index.weight", $detail->weight ?? '') }}" class="form-control">
        </div>
        
    </div>
</div>

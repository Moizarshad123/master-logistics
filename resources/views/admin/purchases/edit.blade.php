@extends('admin.layouts.app')
@section('title', 'Edit Purchase')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Update Sales</h3>
    <form action="{{ route('admin.purchases.update', $salesSheet->id) }}" method="POST" enctype="multipart/form-data" id="expenseTypeForm">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4">
                <label>Station:</label>
                <input type="text" name="station" value="{{ old('station', $salesSheet->station) }}" class="form-control">
                @error('station') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <label>Per Tonn Rate:</label>
                <input type="text" name="per_ton_rate" value="{{ old('per_ton_rate', $salesSheet->per_ton_rate) }}" class="form-control">
                @error('per_ton_rate') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <label>Type:</label>
                <input type="text" name="type" value="{{ old('type', $salesSheet->type) }}" class="form-control">
                @error('type') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <button type="submit"  style="margin-top: 25px;" class="btn btn-success">Update</button>
            </div>
        </div>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')
 
@endsection

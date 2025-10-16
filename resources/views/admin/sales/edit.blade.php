@extends('admin.layouts.app')
@section('title', 'Edit Sales')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Update Sales</h3>
    <form action="{{ route('admin.sales.update', $salesSheet->id) }}" method="POST" enctype="multipart/form-data" id="expenseTypeForm">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4">
                <label>Station:</label>
                <input type="text" name="station" value="{{ old('station', $salesSheet->station) }}" class="form-control">
                @error('station') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <label>Minimum Rent:</label>
                <input type="text" name="minimum_rent" value="{{ old('minimum_rent', $salesSheet->minimum_rent) }}" class="form-control">
                @error('minimum_rent') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <label>Per Bag Rate:</label>
                <input type="text" name="per_bag_rate" value="{{ old('per_bag_rate', $salesSheet->per_bag_rate) }}" class="form-control">
                @error('per_bag_rate') <p style="color:red">{{ $message }}</p> @enderror
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

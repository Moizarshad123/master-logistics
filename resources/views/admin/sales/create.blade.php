@extends('admin.layouts.app')
@section('title', 'Add Sell')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Add Sell</h3>
    <form action="{{ route('admin.sales.store') }}" method="POST" id="expenseTypeForm">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <label>Station:</label>
                <input type="text" name="station" value="{{ old('station') }}" class="form-control">
                @error('station') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <label>Minimum Rent:</label>
                <input type="number" min="1" name="minimum_rent" value="{{ old('minimum_rent') }}" class="form-control">
                @error('minimum_rent') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <label>Per Bag Rate:</label>
                <input type="number" min="1" name="per_bag_rate" value="{{ old('per_bag_rate') }}" class="form-control">
                @error('per_bag_rate') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <button type="submit"  style="margin-top: 25px;" class="btn btn-success">Add</button>
            </div>
        </div>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')
 
@endsection

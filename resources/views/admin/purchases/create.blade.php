@extends('admin.layouts.app')
@section('title', 'Add Purchase')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Add Purchase</h3>
    <form action="{{ route('admin.purchases.store') }}" method="POST" id="expenseTypeForm">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <label>Station:</label>
                <input type="text" name="station" value="{{ old('station') }}" class="form-control">
                @error('station') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <label>Per Ton Rate:</label>
                <input type="number" min="1" name="per_ton_rate" value="{{ old('per_ton_rate') }}" class="form-control">
                @error('per_ton_rate') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <label>Type:</label>
                <input type="text" name="type" value="{{ old('type') }}" class="form-control">
                @error('type') <p style="color:red">{{ $message }}</p> @enderror
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

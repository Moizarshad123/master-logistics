@extends('admin.layouts.app')
@section('title', 'Add Material')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Add Material</h3>
    <form action="{{ route('admin.materials.store') }}" method="POST" id="expenseTypeForm">
        @csrf
        <div class="row">
            <div class="col-md-10">
                <label>Material:</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                @error('name') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-2">
                <button type="submit"  style="margin-top: 25px;" class="btn btn-success">Update</button>
            </div>
        </div>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')
 
@endsection

@extends('admin.layouts.app')
@section('title', 'Update Customer')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Update Customer</h3>
    <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data" id="expenseTypeForm">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-8">
                <label>Name:</label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="form-control">
                @error('name') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <button type="submit"  style="margin-top: 25px;" class="btn btn-success" id="addExpenseType">Update</button>
            </div>
        </div>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')
 
@endsection

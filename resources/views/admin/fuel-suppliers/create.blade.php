@extends('admin.layouts.app')
@section('title', 'Add Fuel Supplier')

@section('content')

<h3>Add Fuel Supplier</h3>

<form action="{{ route('admin.fuel-suppliers.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control" required>
    </div>

    <button class="btn btn-success">Add</button>
    <a href="{{ route('admin.fuel-suppliers.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection

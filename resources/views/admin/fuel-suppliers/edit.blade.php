@extends('admin.layouts.app')
@section('title', 'Edit Fuel Supplier')

@section('content')

<h3>Edit Fuel Supplier</h3>

<form action="{{ route('admin.fuel-suppliers.update', $fuelSupplier->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label>Name <span class="text-danger">*</span></label>
        <input type="text" name="name"
               value="{{ $fuelSupplier->name }}"
               class="form-control" required>
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="{{ route('admin.fuel-suppliers.index') }}" class="btn btn-secondary">Back</a>
</form>

@endsection

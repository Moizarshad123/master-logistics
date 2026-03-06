@extends('admin.layouts.app')
@section('title', 'Edit Inventory Item')

@section('content')
<div class="content">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Edit Inventory Item</h3>
        <a href="{{ route('admin.inventory-items.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <div class="card" style="max-width: 600px;">
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.inventory-items.update', $inventoryItem) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $inventoryItem->name) }}" placeholder="Enter item name">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Make</label>
                    <input type="text" name="make" class="form-control @error('make') is-invalid @enderror"
                           value="{{ old('make', $inventoryItem->make) }}" placeholder="e.g. Toyota, Honda">
                    @error('make')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Model</label>
                    <input type="text" name="model" class="form-control @error('model') is-invalid @enderror"
                           value="{{ old('model', $inventoryItem->model) }}" placeholder="e.g. Corolla, Civic">
                    @error('model')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Unit</label>
                    <select name="unit" class="form-select @error('unit') is-invalid @enderror">
                        <option value="pcs" {{ $inventoryItem->unit == "pcs" ? "selected" : ""}}>Piece</option>
                        <option value="ltr" {{ $inventoryItem->unit == "ltr" ? "selected" : ""}}>Litre</option>
                        <option value="kg" {{ $inventoryItem->unit == "kg" ? "selected" : ""}}>Kg</option>
                    </select>
                    @error('unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Item
                    </button>
                    <a href="{{ route('admin.inventory-items.index') }}" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
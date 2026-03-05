@extends('admin.layouts.app')

@section('title', 'Edit Inventory Item')

@section('content')
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">Edit Item</h4>
            <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
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

            <form action="{{ route('admin.inventories.update', $inventory->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Item Name <span class="text-danger">*</span></label>
                        <input type="text" name="item_name" class="form-control @error('item_name') is-invalid @enderror"
                               value="{{ old('item_name', $inventory->item_name) }}" required>
                        @error('item_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Make</label>
                        <input type="text" name="make" class="form-control @error('make') is-invalid @enderror"
                               value="{{ old('make', $inventory->make) }}">
                        @error('make') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Model</label>
                        <input type="text" name="model" class="form-control @error('model') is-invalid @enderror"
                               value="{{ old('model', $inventory->model) }}">
                        @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                       <label>Unit Price (Rs.) <span class="text-danger">*</span></label>
                       <input type="number" step="0.01" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror"
                              value="{{ old('price', $inventory->unit_price) }}" placeholder="0.00" required>
                       @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                   </div>
                    <div class="col-md-6 mb-3">
                        <label>Total Price (Rs.) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="price" class="form-control @error('price') is-invalid @enderror"
                               value="{{ old('price', $inventory->price) }}" required>
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>


                    <div class="col-md-6 mb-3">
                        <label>Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror"
                               value="{{ old('purchase_date', $inventory->purchase_date->format('Y-m-d')) }}" required>
                        @error('purchase_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="qty" class="form-control @error('qty') is-invalid @enderror"
                               value="{{ old('qty', $inventory->qty) }}" min="0" required>
                        @error('qty') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-sync-alt"></i> Update Item
                </button>
                <a href="{{ route('admin.inventories.index') }}" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>
@endsection
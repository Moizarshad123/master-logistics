@extends('admin.layouts.app')
@section('title', 'Fuel Suppliers')

@section('content')

<div class="row mb-3">
    <div class="col-md-10">
        <h3>Fuel Suppliers</h3>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.fuel-suppliers.create') }}"
           class="btn btn-success btn-sm">+ Add Fuel Supplier</a>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Amount Payable</th>
            <th width="180">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($fuelSuppliers as $supplier)
            <tr>
                <td>{{ $supplier->id }}</td>
                <td>{{ $supplier->name }}</td>
                <td>{{ number_format($supplier->outstanding_amount) }}</td>
                <td>

                    <a href="{{route('admin.fuel-suppliers.show', $supplier->id) }}" class="btn btn-sm btn-info">View Payment History</a>

                    <a href="{{ route('admin.fuel-suppliers.edit', $supplier->id) }}"
                       class="btn btn-warning btn-sm">Edit</a>

                    <form action="{{ route('admin.fuel-suppliers.destroy', $supplier->id) }}"
                          method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this Fuel Supplier?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection

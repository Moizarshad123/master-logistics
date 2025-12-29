@extends('admin.layouts.app')
@section('title','Master Sweetner')

@section('content')

<div class="row mb-3">
    <div class="col-md-10">
        <h3>Master Sweetner</h3>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.master-sweetners.create') }}" class="btn btn-success btn-sm">+ Add</a>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Total Litres</th>
            <th>Per Litre Price</th>
            <th>Total Amount</th>
            <th>Date</th>
            <th>Fuel Type</th>
            <th>Receipt</th>
            <th>Challan</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sweetners as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->supplier->name ?? '' }}</td>
                <td>{{ number_format($row->total_litres) ?? 0 }}</td>
                <td>{{ $row->per_litre_price }}</td>
                <td>{{ number_format($row->total_amount) ?? 0 }}</td>
                <td>{{ date('d M Y', strtotime($row->date)) }}</td>
                <td>{{ $row->fuel_type }}</td>
                <td>
                    @if($row->receiving_receipt)
                        <a href="{{ $row->receiving_receipt }}" target="_blank">View</a>
                    @endif
                </td>
                <td>
                    @if($row->delivery_challan)
                        <a href="{{ $row->delivery_challan }}" target="_blank">View</a>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.master-sweetners.edit',$row->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('admin.master-sweetners.destroy',$row->id) }}" method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection

@extends('admin.layouts.app')
@section('title','Account Payables')

@section('content')

<div class="row mb-3">
    <div class="col-md-10">
        <h3>Account Payables</h3>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.amount-payables.create') }}"
           class="btn btn-success btn-sm">+ Add</a>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Amount</th>
            <th>Payment Via</th>
            <th>Date</th>
            <th>Receipt</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($records as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->supplier->name ?? '' }}</td>
                <td>{{ number_format($row->amount) }}</td>
                <td>{{ $row->payment_via }}</td>
                <td>{{ $row->date }}</td>
                <td>
                    @if($row->receipt)
                        <a href="{{ $row->receipt }}" target="_blank">View</a> |
                        <a href="{{ $row->receipt }}" download>Download</a>
                    @endif
                </td>
                <td>


                    <a href="{{ route('admin.amount-payables.edit',$row->id) }}"
                       class="btn btn-warning btn-sm">Edit</a>

                    <form action="{{ route('admin.amount-payables.destroy',$row->id) }}"
                          method="POST" style="display:inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete?')">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection

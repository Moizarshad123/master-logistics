@extends('admin.layouts.app')
@section('title', 'Loan')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
<style>
    .align{
        text-align: center;
    }
</style>
@endsection
@section('content')
<div class="row mb-3">
    <div class="col-md-10">
        <h3>Loan</h3>
    </div>
    <div class="col-md-2">
        <a href="{{ route('admin.loans.create') }}"
           class="btn btn-success btn-sm">+ Add New Loan</a>
    </div>
</div>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th class="align">Tenure From</th>
            <th class="align">Tenure To</th>
            <th class="align">Loan Amount</th>
            <th class="align">Total Months</th>
            <th class="align">Monthly Installment</th>
            <th class="align">Status</th>
            <th width="180">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($loans as $loan)
            <tr>
                <td>{{ $loan->id }}</td>
                <td>{{ $loan->driver->name ?? "" }}</td>
                <td class="align">{{ date("M Y", strtotime($loan->tenure_from)) }}</td>
                <td class="align">{{ date("M Y", strtotime($loan->tenure_to)) }}</td>
                <td class="align">{{ number_format($loan->amount) }}</td>
                <td class="align">{{ $loan->total_months }}</td>
                <td class="align">{{ number_format($loan->monthly_installment) }}</td>
                <td class="align">
                    @if($loan->status == "paid")
                    <span class="badge badge-success">Paid</span>
                    @else  
                    <span class="badge badge-danger">Unpaid</span>
                    @endif

                </td>
                <td>

                    <a href="{{route('admin.loans.show', $loan->id) }}" class="btn btn-sm btn-info">View Loan History</a>

                    {{-- <a href="{{ route('admin.loans.edit', $loan->id) }}"
                       class="btn btn-warning btn-sm">Edit</a> --}}

                    <form action="{{ route('admin.loans.destroy', $loan->id) }}"
                          method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Delete this Loan?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>


@endsection
{{-- resources/views/admin/overheads/show.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Overhead Detail')

@section('content')

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10"><h3>Overhead Detail</h3></div>
            <div class="col-md-2">
                <a href="{{ route('admin.overheads.index') }}" class="btn btn-sm btn-secondary">Back</a>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="200">Expense Type</th>
                    <td>{{ $overhead->expenseType->name ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Driver</th>
                    <td>
                        @if($overhead->driver)
                            {{ $overhead->driver->name }}
                            <small class="text-muted">({{ $overhead->driver->emp_id }})</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Amount</th>
                    <td>Rs. {{ number_format($overhead->amount, 2) }}</td>
                </tr>
                <tr>
                    <th>Date</th>
                    <td>{{ $overhead->date->format('d-M-Y') }}</td>
                </tr>
                <tr>
                    <th>Comment</th>
                    <td>{{ $overhead->comment ?? '—' }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $overhead->created_at->format('d-M-Y H:i') }}</td>
                </tr>
            </table>

            <a href="{{ route('admin.overheads.edit', $overhead->id) }}" class="btn btn-warning">Edit</a>
        </div>
    </div>

@endsection
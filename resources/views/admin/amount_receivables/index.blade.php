@extends('admin.layouts.app')
@section('title', 'Account Receivables')

@section('content')

<div class="row mb-2">
    <div class="col-md-10"><h3>Account Receivables</h3></div>
    <div class="col-md-2">
        <a href="{{ route('admin.amount-receivables.create') }}" class="btn btn-success btn-sm">
            + Add
        </a>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Trip</th>
            <th>Payment Type</th>
            <th>Amount</th>
            <th>Payment Date</th>
            <th>Receipt</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($receivables as $r)
        <tr>
            <td>{{ $r->id }}</td>
            <td>{{ $r->customer->name }}</td>
            <td>{{ $r->trip_id ?? '-' }}</td>
            <td>
                @if($r->payment_type == "other source")
                    {{  ucfirst($r->payment_type.' - '.$r->other_source) }}
                @else
                    {{  ucfirst($r->payment_type)}}
                @endif
            </td>
            <td>{{ number_format($r->amount) }}</td>
            <td>{{ date('d M Y', strtotime($r->date)) }}</td>

            <td>
                @if($r->receipt)
                    <a href="{{ asset($r->receipt) }}" target="_blank">View</a> |
                    <a href=" {{ asset($r->receipt) }}" download>Download</a>
                @endif
            </td>
            <td>
                <a href="{{ route('admin.amount-receivables.edit',$r->id) }}" class="btn btn-warning btn-sm">Edit</a>
                <form method="POST" action="{{ route('admin.amount-receivables.destroy',$r->id) }}" style="display:inline">
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

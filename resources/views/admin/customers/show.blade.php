@extends('admin.layouts.app')
@section('title', 'Customer Payment History')

@section('content')

<div class="row mb-2">
    <div class="col"><h3>Customer Payment History</h3></div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Trip ID</th>
            <th>Payment Type</th>
            <th>Amount</th>
            <th>Payment Date</th>
            <th>Receipt</th>
        </tr>
    </thead>
    <tbody>
        @foreach($payments as $r)
        <tr>
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
        </tr>
        @endforeach
    </tbody>
</table>
@endsection

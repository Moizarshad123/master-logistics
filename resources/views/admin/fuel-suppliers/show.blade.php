@extends('admin.layouts.app')
@section('title', 'Amount Payable History')

@section('content')

<div class="row mb-3">
    <div class="col">
        <h3>Amount Payable History</h3>
    </div>
</div>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Amount</th>
            <th>Date</th>
            <th>Payment Via</th>
            <th>Receipt</th>
        </tr>
    </thead>
    <tbody>
        @foreach($history as $his)
            <tr>
                <td>{{ number_format($his->amount) }}</td>
                <td>{{ date('d M Y', strtotime($his->date)) }}</td>
                <td>
                    @if($his->payment_via == "Other Source")
                        {{  ucfirst($his->payment_via.' - '.$his->other_source) }}
                    @else
                        {{  ucfirst($his->payment_via)}}
                    @endif
                </td>
                <td>
                    @if($his->receipt)
                        <a href="{{ asset($his->receipt) }}" target="_blank">View</a> |
                        <a href=" {{ asset($his->receipt) }}" download>Download</a>
                    @endif
                </td>
                    
            </tr>
        @endforeach
    </tbody>
</table>

@endsection

@extends('admin.layouts.app')
@section('title', 'Loan History')

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
        <div class="col">
            <h3>Loan History</h3>
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Month & Year</th>
                <th>Amount</th>
                <th class="align">Paid Date</th>
                <th class="align">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($installments as $loan)
                <tr>
                    <td>{{ date(($loan->month)).' - '.$loan->year }}</td>
                    <td>{{ round($loan->amount) ?? "" }}</td>
                    <td class="align">{{ isset($loan->paid_at) ? date("d M Y", strtotime($loan->paid_at)) : "" }}</td>
                    <td class="align">
                        @if($loan->status == "paid")
                            <span class="badge badge-success">Paid</span>
                        @else  
                            <span class="badge badge-danger">Unpaid</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
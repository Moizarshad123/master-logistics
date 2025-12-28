@extends('admin.layouts.app')
@section('title', 'Customer Heads Report')

@section('content')

    <div class="row">
        <div class="col-md-10">
            <h3>Customer Heads Report</h3>
        </div>
        <div class="col-md-2">
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $head)
                <tr>
                    <td>{{ $head["id"] ?? '' }}</td>
                    <td>{{ $head["customer_head"] ?? '' }}</td>
                    <td>{{ $head["total_outstanding"] ?? 0 }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

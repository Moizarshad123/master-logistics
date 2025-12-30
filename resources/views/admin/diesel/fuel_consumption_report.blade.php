@extends('admin.layouts.app')
@section('title','Fuel Consumption Report')

@section('content')

<div class="row mb-3">
    <div class="col-md-8">
        <h3>Fuel Consumption Report</h3>
    </div>
    <div class="col-md-4 text-end">
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            Print Report
        </button>
    </div>
</div>

<form method="GET" class="row mb-3">
    <div class="col-md-3">
        <label>From Date</label>
        <input type="date" name="from_date" value="{{ $from }}" class="form-control">
    </div>

    <div class="col-md-3">
        <label>To Date</label>
        <input type="date" name="to_date" value="{{ $to }}" class="form-control">
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-success">Filter</button>
    </div>
</form>

<div id="printArea">
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Vehicle</th>
            <th>Type</th>
            <th>Source</th>
            <th>Total Litres</th>
            <th>Per Litre Amount</th>
            <th>Total Amount</th>
        </tr>
    </thead>
    <tbody>
        @php $grandLitres = 0; $grandAmount = 0; @endphp

        @foreach($records as $row)
            @php
                $grandLitres += $row->total_litres;
                $grandAmount += $row->total_amount;
            @endphp
            <tr>
                <td>{{ $row->vehicle->vehicle_no ?? '' }}</td>
                <td>{{ $row->type }}</td>
                <td>{{ $row->source }}</td>
                <td>{{ number_format($row->total_litres,2) }}</td>
                <td>{{ number_format($row->per_litre_amount,2) }}</td>
                <td>{{ number_format($row->total_amount,2) }}</td>
            </tr>
        @endforeach
    </tbody>

    <tfoot>
        <tr style="font-weight:bold;background:#f2f2f2">
            <td colspan="3">Grand Total</td>
            <td>{{ number_format($grandLitres,2) }}</td>
            <td></td>
            <td>{{ number_format($grandAmount,2) }}</td>
        </tr>
    </tfoot>
</table>
</div>

@endsection

<style>
@media print {
    body * { visibility: hidden; }
    #printArea, #printArea * { visibility: visible; }
    #printArea { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>

@extends('admin.layouts.app')
@section('title', 'Trip Cash Allowance Slip')

@section("css")
<style>
    body { background:#fff; }
    .slip-box {
        border: 2px solid #000;
        padding: 15px;
        margin-bottom: 25px;
        page-break-after: always;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    th, td {
        border: 1px solid #000;
        padding: 6px 8px;
        vertical-align: middle;
    }
    .no-border td {
        border: none !important;
        padding: 4px;
    }
    h3 {
        text-align: center;
        margin-bottom: 10px;
        text-transform: uppercase;
    }
    @media print {
        .no-print { display: none; }
    }
</style>
@endsection

@section('content')

<div class="content">

@foreach($trips as $trip)
<div class="slip-box">

    <h3>Trip Cash Allowance Disbursement Slip</h3>

    {{-- BASIC INFO --}}
    <table class="no-border">
        <tr>
            <td><strong>Vehicle No:</strong> {{ $trip->vehicle->vehicle_no ?? '-' }}</td>
            <td><strong>Date:</strong> {{ date('d-m-Y', strtotime($trip->trip_date)) }}</td>
        </tr>
        <tr>
            <td><strong>Trip ID:</strong> {{ $trip->trip_no ?? $trip->id }}</td>
            <td><strong>Driver:</strong> {{ $trip->driver->name ?? '-' }}</td>
        </tr>
    </table>

    <br>

    {{-- TRIP DETAILS --}}
    <table>
        <thead>
            <tr>
                <th style="width:25%">From → To</th>
                <th style="width:25%">Material</th>
                <th style="width:15%">Qty / Weight</th>
                <th style="width:35%">Remarks</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trip->tripDetails as $detail)
            <tr>
                <td>
                    {{ $detail->tripDetails->from_destination ?? '-' }}
                    →
                    {{ $detail->tripDetails->to_destination ?? '-' }}
                </td>
                <td>{{ $detail->material ?? '-' }}</td>
                <td>
                    {{ $detail->total_bags ?? $detail->weight ?? '-' }}
                </td>
                <td>{{ $detail->comments ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No Trip Details Found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <br>

    {{-- CASH ALLOWANCE --}}
    <table>
        <tr>
            <th style="width:40%">Cash Allowance Amount</th>
            <td style="width:60%">
                {{ number_format($trip->total_expense ?? 0, 2) }}
            </td>
        </tr>
    </table>

    <br><br>

    {{-- SIGNATURE --}}
    <table class="no-border">
        <tr>
            <td style="width:60%">
                <strong>Supervisor Sign & Stamp:</strong>
            </td>
            <td style="width:40%">
                ___________________________
            </td>
        </tr>
    </table>

</div>
@endforeach

<div class="no-print text-center">
    <button onclick="window.print()" class="btn btn-primary">🖨 Print</button>
</div>

</div>
@endsection

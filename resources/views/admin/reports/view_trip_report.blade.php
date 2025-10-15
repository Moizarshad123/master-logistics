@extends('admin.layouts.app')
@section('title', 'Trip Vehicle Report')


@section("css")
<style>
    /* ===== Print-Friendly Styling ===== */
    .report-container {
        background: #fff;
        padding: 15px;
        border: 1px solid #000;
        margin: 0 auto;
    }
    table.report-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
    }
    table.report-table th,
    table.report-table td {
        border: 1px solid #000 !important;
        padding: 6px 10px;
        font-size: 14px;
        text-align: left;
        vertical-align: middle;
    }
    table.report-table th {
        background: #f5f5f5;
        font-weight: bold;
    }
    .trip-header th,
    .trip-header td {
        border: none !important;
        font-weight: bold;
        font-size: 15px;
        background: transparent;
        padding: 5px 8px;
    }
    h3.report-title {
        text-align: center;
        margin-bottom: 15px;
        text-transform: uppercase;
        font-weight: bold;
    }
    @media print {
        body {
            margin: 0;
            background: #fff;
        }
        .no-print {
            display: none !important;
        }
        .report-container {
            border: none;
            box-shadow: none;
            padding: 0;
        }
    }
</style>
@endsection
@section('content')

<div class="content">
    <div class="table-responsive report-container">
        <h3 class="report-title">Trip Vehicle Report</h3>    
        <table class="report-table trip-header table table-custom table-lg mb-0" id="ordersTable">
            <tr>
                <th><strong>Date</strong></th>
                <td>{{ date('d-M-Y', strtotime( $trip->created_at))}}</td>
                <th><strong>Tr. ID</strong></th>
                <td>{{ $trip->trip_no ?? ""}}</td>
            </tr>
            <tr>
                <th><strong>Driver Name</strong></th>
                <td>{{ $trip?->driver?->name ?? ""}}</td>
                <th><strong>Vehicle #</strong></th>
                <td>{{ $trip?->vehicle?->vehicle_no ?? ""}}</td>
            </tr>
        </table>

        Payments
        @if(count($trip->tripPayments) > 0)
            <table class="table table-custom table-bordered table-sm mb-4 report-table">
                <thead>
                    <tr>
                        <th>Payment Type</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Comment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trip->tripPayments as $item)
                        <tr>
                            <td>{{ date('d M Y', strtotime($item->date)) ?? "" }}</td>
                            <td>{{ $item->payment_type ?? ""}}</td>
                            <td>{{ $item->amount ?? ""}}</td>
                            <td>{{ $item->comments ?? ""}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Trip 1 Details --}}
        @if(count($trip->tripDetails) > 0)
            @foreach ($trip->tripDetails as $item)
                <table class="table table-custom table-bordered table-sm mb-4 report-table">
                    <thead>
                        <tr>
                            <th colspan="2" style="background-color: #f8f9fa;"><strong>Trip {{ $loop->iteration }} Details (Tr. ID: {{ $item->id }})</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th style="width: 25%;">From</th>
                            <td style="width: 25%;">{{ $item->from_destination ?? ""}}</td>
                            <th style="width: 25%;">To</th>
                            <td style="width: 25%;">{{ $item->to_destination ?? ""}}</td>
                        </tr>
                        <tr>
                            <th>Material</th>
                            <td>{{ $item->material ?? ""}}</td>
                            <th>{{ $item->material_type ?? ""}}</th>
                            <td>{{ $item->total_bags ?? ""}}</td>
                        </tr>
                        {{-- <tr>
                            <th>Baloch Labour</th>
                            <td>{{ $item->baloch_labour ?? ""}}</td>
                            <th>Weekly Labour</th>
                            <td>{{ $item->weekly_labour ?? ""}}</td>
                        </tr> --}}
                        <tr>
                            {{-- <th>Advance</th>
                            <td>{{ $item->advance ?? ""}}</td> --}}
                            <th>Rent</th>
                            <td>{{ $item->rent ?? ""}}</td>
                            <th>Weight</th>
                            <td>{{ $item->weight ?? ""}}</td>
                        </tr>
                      
                    </tbody>
                </table>
            @endforeach
        @endif

        {{-- Remarks --}}
        <table class="table table-custom table-sm mb-0">
            <tr>
                <th><strong>Remarks:</strong></th>
                <td></td> {{-- Leave blank for remarks --}}
            </tr>
        </table>

        <div class="no-print" style="text-align:center; margin-top:10px;">
            <button onclick="window.print()" style="padding: 6px 12px; background:#007bff; color:#fff; border:none; border-radius:4px; cursor:pointer;">
                🖨 Print
            </button>
        </div>
    </div>
</div>
@endsection

@section('js')


@endsection

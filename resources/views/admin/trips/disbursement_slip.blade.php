@extends('admin.layouts.app')
@section('title', 'Trip Cash Allowance Slip')

@section("css")
<style>
    body {
        background: #fff;
    }

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

    th,
    td {
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
        .no-print {
            display: none;
        }
    }

</style>
@endsection

@section('content')

<div class="content">

    <div class="no-print mb-3">
        <form method="GET" action="{{ route('admin.disbursementSlip') }}">
            <div class="row">
                <div class="col-md-6">
                    <input type="number" name="trip_id" class="form-control"
                        placeholder="Enter Trip ID"
                        value="{{ request('trip_id') }}" required>

                </div>
                <div class="col-md-2 text-end">
  <button type="submit" class="btn btn-primary">
                🔍 Search
            </button>
                </div>

                <div class="col-md-2">
                         @if(isset($trip))
                <button type="button" onclick="window.print()" class="btn btn-primary">
                    🖨 Print Slip
                </button>
            @endif
                </div>
            </div>

          

       
        </form>
    </div>

    @if(isset($trip))
        <div class="slip-box">

            <h3>Trip Cash Allowance Disbursement Slip</h3>
            {{-- BASIC INFO --}}
            <table class="no-border">
                <tr>
                    <td><strong>Vehicle No:</strong> {{ $trip->vehicle->vehicle_no ?? '-' }}</td>
                    <td><strong>Date:</strong> {{ date('d-m-Y', strtotime($trip->trip_date)) ?? '' }}</td>
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
                            {{ $detail->from_destination ?? '-' }}
                            →
                            {{ $detail->to_destination ?? '-' }}
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
                        {{ number_format($advance_amount ?? 0, 2) }}
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
    @endif

</div>
@endsection

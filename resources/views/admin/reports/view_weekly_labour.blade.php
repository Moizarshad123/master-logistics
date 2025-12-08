@extends('admin.layouts.app')
@section('title', 'Weekly Labour Report')


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
            <h3 class="report-title">Weekly Labour Report</h3>    
        
            {{-- Trip 1 Details --}}
            @if(count($reports) > 0)
                <table class="table table-custom table-bordered table-sm mb-4 report-table">
                    <thead>
                        <tr>
                            {{-- <th style="background-color: #f8f9fa;"><strong>Trip ID</strong></th> --}}
                            <th style="background-color: #f8f9fa;">Vehicle</th>
                            <th style="background-color: #f8f9fa;">Weekly Labour Amount</th>
                        </tr>
                    </thead>
                        @foreach ($reports as $item)
                            <tbody>
                                <tr>
                                    {{-- <th style="width: 25%;">{{ $item->trip_id }}</th> --}}
                                    <td style="width: 25%;">{{ $item->trip->vehicle->vehicle_no ?? ""}}</td>
                                    <td style="width: 25%;">{{ $item->total_weekly_labour ?? ""}}</td>
                                </tr>
                            
                            </tbody>
                        @endforeach
                </table>
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

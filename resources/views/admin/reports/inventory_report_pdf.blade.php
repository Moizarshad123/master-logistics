<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1a1a2e;
            margin: 0;
            padding: 10px;
        }

        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .report-subtitle {
            text-align: center;
            font-size: 10px;
            color: #666;
            margin-bottom: 4px;
        }

        .date-range {
            text-align: center;
            font-size: 10px;
            margin-bottom: 14px;
            color: #333;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            background: #1a1a2e;
            color: white;
            padding: 5px 10px;
            margin-bottom: 0;
            margin-top: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th {
            background: #f0f4f8;
            padding: 5px 6px;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        td {
            padding: 4px 6px;
            text-align: center;
            font-size: 9px;
        }

        tbody tr:nth-child(even) {
            background: #f8f9fc;
        }

        .total-row {
            font-weight: bold;
            background: #e2e8f0 !important;
        }

        .stock-low  { color: #dc3545; font-weight: bold; }
        .stock-ok   { color: #28a745; font-weight: bold; }

        .summary-table {
            width: 100%;
            margin-bottom: 14px;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 5px 10px;
            font-size: 10px;
            border: 1px solid #ccc;
        }

        .summary-table .label-cell {
            background: #f0f4f8;
            font-weight: bold;
            width: 40%;
        }

        .divider {
            border: none;
            border-top: 2px solid #1a1a2e;
            margin: 12px 0;
        }
    </style>
</head>
<body>

    <div class="report-title">Inventory Report</div>
    <div class="report-subtitle">Generated on: {{ now()->format('d M Y, H:i A') }}</div>

    @if(request('from_date'))
    <div class="date-range">
        Period: {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}
        &nbsp;—&nbsp;
        {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}
    </div>
    @endif

    <hr class="divider">

    {{-- SUMMARY BOXES --}}
    <table class="summary-table">
        <tr>
            <td class="label-cell">Total Item Types</td>
            <td>{{ $summary['total_items'] }}</td>
            <td class="label-cell">Total Purchased (Qty)</td>
            <td>{{ number_format($summary['total_purchased_qty']) }}</td>
        </tr>
        <tr>
            <td class="label-cell">Total Issued (Qty)</td>
            <td>{{ number_format($summary['total_issued_qty']) }}</td>
            <td class="label-cell">Total Purchase Amount</td>
            <td><strong>{{ number_format($summary['total_amount']) }}</strong></td>
        </tr>
    </table>

    {{-- PURCHASE RECORDS --}}
    <div class="section-title">Purchase Records</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item Name</th>
                <th>Make / Model</th>
                <th>Unit</th>
                <th>Invoice No</th>
                <th>Vendor</th>
                <th>Purchase Date</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total Price</th>
                <th>Remaining Qty</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $i => $inv)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="text-align:left; font-weight:bold;">{{ $inv->item->name ?? '-' }}</td>
                <td>{{ $inv->item->make ?? '' }} {{ $inv->item->model ?? '' }}</td>
                <td>{{ $inv->item->unit ?? '-' }}</td>
                <td>{{ $inv->invoice_no ?? '-' }}</td>
                <td>{{ $inv->vendor ?? '-' }}</td>
                <td>{{ $inv->purchase_date ? $inv->purchase_date->format('d M Y') : '-' }}</td>
                <td>{{ number_format($inv->qty) }}</td>
                <td>{{ number_format($inv->unit_price) }}</td>
                <td><strong>{{ number_format($inv->total_price) }}</strong></td>
                <td class="{{ $inv->remaining_qty <= 0 ? 'stock-low' : 'stock-ok' }}">
                    {{ number_format($inv->remaining_qty) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="11" style="text-align:center; color:#999;">No records found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" style="text-align:right;">Totals:</td>
                <td>{{ number_format($inventories->sum('qty')) }}</td>
                <td>—</td>
                <td>{{ number_format($inventories->sum('total_price')) }}</td>
                <td>{{ number_format($inventories->sum('remaining_qty')) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- STOCK SUMMARY --}}
    <div class="section-title" style="margin-top:16px;">Stock Summary by Item</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Item Name</th>
                <th>Make / Model</th>
                <th>Unit</th>
                <th>Total Purchased</th>
                <th>Total Issued</th>
                <th>Remaining Stock</th>
                <th>Total Value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockSummary as $i => $stock)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="text-align:left; font-weight:bold;">{{ $stock->name }}</td>
                <td>{{ $stock->make ?? '' }} {{ $stock->model ?? '' }}</td>
                <td>{{ $stock->unit ?? '-' }}</td>
                <td>{{ number_format($stock->total_purchased) }}</td>
                <td>{{ number_format($stock->total_issued) }}</td>
                <td class="{{ $stock->remaining_stock <= 0 ? 'stock-low' : 'stock-ok' }}">
                    {{ number_format($stock->remaining_stock) }}
                </td>
                <td>{{ number_format($stock->total_value) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; color:#999;">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
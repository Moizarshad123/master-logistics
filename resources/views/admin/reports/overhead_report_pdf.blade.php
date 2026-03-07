<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Overhead Report</title>
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

        .divider {
            border: none;
            border-top: 2px solid #1a1a2e;
            margin: 10px 0;
        }

        .section-title {
            font-size: 11px;
            font-weight: bold;
            background: #1a1a2e;
            color: white;
            padding: 5px 10px;
            margin-top: 14px;
            margin-bottom: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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
            padding: 4px 7px;
            font-size: 9px;
            text-align: center;
        }

        tbody tr:nth-child(even) { background: #f8f9fc; }

        .total-row {
            font-weight: bold;
            background: #e2e8f0 !important;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 12px;
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
            width: 30%;
        }

        td.left { text-align: left; }
        td.right { text-align: right; }
    </style>
</head>
<body>

    <div class="report-title">Overhead Report</div>
    <div class="report-subtitle">Generated on: {{ now()->format('d M Y, H:i A') }}</div>

    @if(request('from_date'))
    <div class="date-range">
        Period: {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}
        &mdash;
        {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}
    </div>
    @endif

    <hr class="divider">

    {{-- Summary --}}
    <table class="summary-table">
        <tr>
            <td class="label-cell">Total Records</td>
            <td>{{ $summary['total_records'] }}</td>
            <td class="label-cell">Total Amount</td>
            <td><strong>{{ number_format($summary['total_amount']) }}</strong></td>
        </tr>
        <tr>
            <td class="label-cell">Unique Expense Types</td>
            <td>{{ $summary['unique_types'] }}</td>
            <td class="label-cell">Avg Per Day</td>
            <td>{{ number_format($summary['avg_per_day']) }}</td>
        </tr>
    </table>


    {{-- Detailed Records --}}
    <div class="section-title" style="margin-top:16px;">Detailed Overhead Records</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Expense Type</th>
                <th>Category</th>
                <th>Driver</th>
                <th>Amount</th>
                <th>Comment</th>
            </tr>
        </thead>
        <tbody>
            @forelse($overheads as $i => $overhead)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $overhead->date ? $overhead->date->format('d M Y') : '-' }}</td>
                <td class="left"><strong>{{ $overhead->expenseType->name ?? '-' }}</strong></td>
                <td>{{ $overhead->expenseType?->category?->name ?? '—' }}</td>
                <td>{{ $overhead->driver->name ?? '—' }}</td>
                <td><strong>{{ number_format($overhead->amount) }}</strong></td>
                <td class="left" style="color:#555;">{{ $overhead->comment ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center; color:#999;">No records found</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="right">Total:</td>
                <td>{{ number_format($overheads->sum('amount')) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
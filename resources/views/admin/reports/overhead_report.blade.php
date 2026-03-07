@extends('admin.layouts.app')
@section('title', 'Overhead Report')

@section('css')
<style>
    .report-header-card {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        color: white;
    }

    .report-header-card h3 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 4px;
        letter-spacing: 0.5px;
    }

    .report-header-card p {
        font-size: 13px;
        opacity: 0.7;
        margin: 0;
    }

    .date-range-badge {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 4px 14px;
        font-size: 12px;
        display: inline-block;
        margin-top: 8px;
    }

    .filter-card {
        background: #fff;
        border-radius: 10px;
        padding: 20px 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        margin-bottom: 20px;
        border: 1px solid #e8edf3;
    }

    .filter-card label {
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 6px;
        display: block;
    }

    .filter-card .form-control {
        border-radius: 8px;
        border: 1.5px solid #dee2e6;
        font-size: 13px;
        height: 38px;
    }

    .filter-card .form-control:focus {
        border-color: #0f3460;
        box-shadow: 0 0 0 3px rgba(15, 52, 96, 0.1);
    }

    .btn-filter {
        background: #0f3460;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 22px;
        font-size: 13px;
        font-weight: 600;
        height: 38px;
        transition: all 0.2s;
        cursor: pointer;
    }

    .btn-filter:hover {
        background: #16213e;
        color: white;
        transform: translateY(-1px);
    }

    .btn-reset {
        background: #f8f9fa;
        color: #6c757d;
        border: 1.5px solid #dee2e6;
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 600;
        height: 38px;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-reset:hover {
        background: #e9ecef;
        color: #495057;
        text-decoration: none;
    }

    .btn-pdf {
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 600;
        height: 38px;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-pdf:hover {
        background: #c82333;
        color: white;
        transform: translateY(-1px);
        text-decoration: none;
    }

    .summary-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .summary-card {
        background: white;
        border-radius: 10px;
        padding: 18px 20px;
        border: 1px solid #e8edf3;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        position: relative;
        overflow: hidden;
    }

    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .summary-card.blue::before {
        background: #0f3460;
    }

    .summary-card.green::before {
        background: #28a745;
    }

    .summary-card.orange::before {
        background: #fd7e14;
    }

    .summary-card.purple::before {
        background: #6f42c1;
    }

    .summary-card .label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #9aa0ac;
        margin-bottom: 8px;
    }

    .summary-card .value {
        font-size: 22px;
        font-weight: 700;
        color: #1a1a2e;
    }

    .table-card {
        background: white;
        border-radius: 10px;
        border: 1px solid #e8edf3;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        margin-bottom: 24px;
    }

    .table-card-header {
        background: #f8f9fc;
        padding: 14px 20px;
        border-bottom: 1px solid #e8edf3;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-card-header h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #1a1a2e;
    }

    .table-card table thead th {
        background: #f0f4f8;
        color: #4a5568;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 14px;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }

    .table-card table tbody td {
        padding: 10px 14px;
        font-size: 13px;
        color: #2d3748;
        border-bottom: 1px solid #f0f4f8;
        vertical-align: middle;
    }

    .table-card table tbody tr:last-child td {
        border-bottom: none;
    }

    .table-card table tbody tr:hover {
        background: #f8f9fc;
    }

    .badge-category {
        background: #e8f0fe;
        color: #0f3460;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #9aa0ac;
        font-size: 14px;
    }

    .no-data i {
        font-size: 36px;
        margin-bottom: 10px;
        display: block;
    }

    tfoot td {
        font-weight: 700;
        background: #f0f4f8;
        padding: 10px 14px;
        font-size: 13px;
    }

</style>
@endsection

@section('content')

{{-- Header --}}
<div class="report-header-card">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h3>💼 Overhead Report</h3>
            @if(request('from_date'))
            <span class="date-range-badge">
                📅 {{ \Carbon\Carbon::parse(request('from_date'))->format('d M Y') }}
                &nbsp;→&nbsp;
                {{ \Carbon\Carbon::parse(request('to_date'))->format('d M Y') }}
            </span>
            @endif
        </div>
        <div>
            <a href="{{ route('admin.overhead.report.pdf', request()->all()) }}" class="btn-pdf" target="_blank">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14,2 14,8 20,8" />
                </svg>
                Download PDF
            </a>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="filter-card">
    <form method="GET" action="{{ route('admin.overheadReport') }}">
        <div class="row align-items-end g-3">
            <div class="col-md-2">
                <label>From Date</label>
                <input type="date" name="from_date" class="form-control"
                    value="{{ request('from_date', now()->startOfMonth()->toDateString()) }}">
            </div>
            <div class="col-md-2">
                <label>To Date</label>
                <input type="date" name="to_date" class="form-control"
                    value="{{ request('to_date', now()->toDateString()) }}">
            </div>
            <div class="col-md-3">
                <label>Expense Type</label>
                <select name="expense_type_id" id="expense_type_id" class="form-control">
                    <option value="">-- All Types --</option>
                    @foreach($expenseTypes as $type)
                    <option value="{{ $type->id }}" {{ request('expense_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                        @if($type->category) ({{ $type->category->name }}) @endif
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label>&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-filter">🔍 Filter</button>
                    <a href="{{ route('admin.overheadReport') }}" class="btn-reset">✕ Reset</a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Summary Cards --}}
<div class="summary-cards">
    <div class="summary-card blue">
        <div class="label">Total Records</div>
        <div class="value">{{ $summary['total_records'] }}</div>
    </div>
    <div class="summary-card green">
        <div class="label">Total Amount</div>
        <div class="value">{{ number_format($summary['total_amount']) }}</div>
    </div>
    <div class="summary-card orange">
        <div class="label">Expense Types</div>
        <div class="value">{{ $summary['unique_types'] }}</div>
    </div>
    <div class="summary-card purple">
        <div class="label">Avg Per Day</div>
        <div class="value">{{ number_format($summary['avg_per_day']) }}</div>
    </div>
</div>

{{-- Detailed Records --}}
<div class="table-card">
    <div class="table-card-header">
        <h5>📋 Detailed Overhead Records</h5>
        <span class="badge bg-secondary">{{ $overheads->count() }} records</span>
    </div>

    @if($overheads->count())
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
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
                    @foreach($overheads as $i => $overhead)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $overhead->date ? $overhead->date->format('d M Y') : '-' }}</td>
                        <td><strong>{{ $overhead->expenseType->name ?? '-' }}</strong></td>
                        <td>
                            @if($overhead->expenseType?->category)
                            <span class="badge-category">{{ $overhead->expenseType->category->name }}</span>
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>{{ $overhead->driver->name ?? '—' }}</td>
                        <td><strong>{{ number_format($overhead->amount) }}</strong></td>
                        <td style="color:#6c757d; font-size:12px;">{{ $overhead->comment ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align:right;">Total:</td>
                        <td>{{ number_format($overheads->sum('amount')) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="no-data">
            <i>📭</i>
            No overhead records found for the selected filters.
        </div>
    @endif
</div>

@endsection

@section('js')
<script>
    $(document).ready(function () {

        $('#expense_type_id').select2({
            placeholder: "Select Item",
            allowClear: true
        });
    });

</script>
@endsection

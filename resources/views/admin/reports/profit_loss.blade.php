@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Daily Profit & Loss Report</h3>

    <form method="GET" class="mb-4">
        <div class="d-flex align-items-center gap-2">
            <label for="date">Select Date:</label>
            <input type="date" name="date" id="date" value="{{ $date }}" class="form-control" style="width:200px">
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        </div>
    </form>

    {{-- Income Table --}}
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>Income (Rent)</th>
            </tr>
        </thead>
        <tbody>
            {{-- @forelse($tripData as $trip)
                <tr>
                    <td class="text-end">{{ number_format($trip['income'], 2) }}</td>
                </tr>
            @empty
                <tr><td class="text-center">No trips found for this date.</td></tr>
            @endforelse --}}
        </tbody>
        <tfoot class="table-secondary fw-bold">
            <tr>
                <td class="text-end">Total Income: {{ number_format($totalIncome, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Expenses Table --}}
    <table class="table table-striped">
        <thead class="table-dark">
            <tr>
                <th>Expense</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
           @php
            $totalWeeklyLabour = 0;
            $totalBalochLabour = 0;
        @endphp

        @forelse($tripData as $trip)
            @php
                // Use object syntax if $trip is a model
                $totalWeeklyLabour += $trip["weekly_labour"] ?? 0;
                $totalBalochLabour += $trip["baloch_labour"] ?? 0;
            @endphp
        @empty
        @endforelse
        <tr>
                        <td>Weekly Labour</td>
                        <td class="text-end">{{ number_format($totalWeeklyLabour, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Baloch Labour</td>
                        <td class="text-end">{{ number_format($totalBalochLabour, 2) }}</td>
                    </tr>
            @forelse($tripData as $trip)
                @if($trip['vehicle_expenses']->count())
                    @foreach($trip['vehicle_expenses'] as $exp)
                        <tr>
                            {{-- Skip if expense is Weekly or Baloch Labour --}}
                            @if(in_array(strtolower($exp["expense"] ?? ''), ['weekly labour', 'baloch labour']))
                                @continue
                            @endif
                            <td>{{ $exp["expense"] ?? 'Expense' }}</td>
                            <td class="text-end">{{ number_format($exp->amount, 2) }}</td>
                        </tr>
                    @endforeach
                @endif
            @empty
                <tr><td colspan="2" class="text-center">No trips found for this date.</td></tr>
            @endforelse
        </tbody>
        <tfoot class="table-secondary fw-bold">
            <tr>
                <td>Total Expenses</td>
                <td class="text-end">{{ number_format($totalExpenses, 2) }}</td>
            </tr>
            <tr>
                <td>Profit / Loss</td>
                <td class="text-end {{ $grandProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($grandProfit, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection

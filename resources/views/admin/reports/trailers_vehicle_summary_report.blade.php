@extends('admin.layouts.app')
@section('title', 'Trailers Vehicle Summary Report')

@section('content')

<div class="content">
    <h3 class="mb-3">Trailers Vehicle Summary Report</h3>

    <form method="GET" action="{{ route('admin.trailersVehicleSummaryReport') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label>From Date</label>
                <input type="date" name="from_date" class="form-control"
                    value="{{ request('from_date') }}">
            </div>

            <div class="col-md-3">
                <label>To Date</label>
                <input type="date" name="to_date" class="form-control"
                    value="{{ request('to_date') }}">
            </div>

            <div class="col-md-6 d-flex align-items-end">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.trailersVehicleSummaryReport') }}" class="btn btn-secondary ms-2">
                    Reset
                </a>
                <a href="{{ route('admin.trailerVehicleSummaryReport.pdf', request()->all()) }}"
                class="btn btn-warning ms-2">
                    Download PDF
                </a>
            </div>
        </div>
    </form>

    {{-- FIRST TABLE: Expenses (Vehicle No to Dynamic Categories) --}}
    @foreach($report as $category => $vehicles)
        @if(strtolower($category) == 'trailers')
            <h4>{{ $category }}</h4>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Vehicle No</th>
                            <th>Trips</th>
                            <th>Total Journey</th>
                            @foreach($expenseCategories as $expCategory)
                                @if($expCategory->name != "Salaries")
                                    <th>{{ $expCategory->name }}</th>
                                @endif
                            @endforeach
                            <th>Advance</th>
                            <th>Maintenance & Workshop</th>
                            <th>Inventory</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicles as $vehicleNo => $data)
                            <tr>
                                <td>{{ $vehicleNo }}</td>
                                <td>{{ $data['trips'] }}</td>
                                <td>{{ $data['total_journeys'] }}</td>
                                @foreach($expenseCategories as $expCategory)
                                    @if($expCategory->name != "Salaries")
                                        <td>{{ number_format($data[$expCategory->name] ?? 0) }}</td>
                                    @endif
                                @endforeach
                                <td>{{ number_format($data['Advance'] ?? 0) }}</td>
                                <td>{{ number_format($data['Maintenance'] ?? 0) }}</td>
                                <td>{{ number_format($data['Inventory'] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    {{-- Grand Total for First Table (Expenses) --}}
    @if(count($report) > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr style="font-weight:bold; background:#e0e0e0;">
                        <td>Grand Total</td>
                        <td>{{ $grandTotal['trips'] }}</td>
                        <td>{{ $grandTotal['total_journeys'] }}</td>
                        @foreach($expenseCategories as $expCategory)
                            @if($expCategory->name != "Salaries")
                                <td>{{ number_format($grandTotal[$expCategory->name] ?? 0) }}</td>
                            @endif
                        @endforeach
                        <td>{{ number_format($grandTotal['Advance'] ?? 0) }}</td>
                        <td>{{ number_format($grandTotal['Maintenance'] ?? 0) }}</td>
                        <td>{{ number_format($grandTotal['Inventory'] ?? 0) }}</td>
                    </tr>

                    {{-- Category-wise Totals for Expenses --}}
                    @foreach($categoryTotals as $category => $totals)
                        @if(strtolower($category) == 'trailers')
                            <tr style="background:#f9f9f9;">
                                <td>{{ $category }}</td>
                                <td>{{ $totals['trips'] }}</td>
                                <td>{{ $totals['total_journeys'] }}</td>
                                @foreach($expenseCategories as $expCategory)
                                    @if($expCategory->name != "Salaries")
                                        <td>{{ number_format($totals[$expCategory->name] ?? 0) }}</td>
                                    @endif
                                @endforeach
                                <td>{{ number_format($totals['Advance'] ?? 0) }}</td>
                                <td>{{ number_format($totals['Maintenance'] ?? 0) }}</td>
                                <td>{{ number_format($totals['Inventory'] ?? 0) }}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <br><br>

    {{-- SECOND TABLE: Financial Summary (Advance removed) --}}
    @foreach($report as $category => $vehicles)
        @if(strtolower($category) == 'trailers')
            <h4>{{ $category }}</h4>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Vehicle No</th>
                            <th>Salary</th>
                            <th>Maintenance & Workshop</th>
                            <th>Inventory</th>
                            <th>Total Exp</th>
                            <th>Sale Rent</th>
                            <th>Gross Earning</th>
                            <th>Net Earning</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehicles as $vehicleNo => $data)
                            <tr>
                                <td>{{ $vehicleNo }}</td>
                                <td>{{ number_format($data['Salary']) }}</td>
                                <td>{{ number_format($data['Maintenance'] ?? 0) }}</td>
                                <td>{{ number_format($data['Inventory'] ?? 0) }}</td>
                                <td>{{ number_format($data['Total_Exp']) }}</td>
                                <td>{{ number_format($data['Sale_Rent']) }}</td>
                                <td>{{ number_format($data['Gross_Earning']) }}</td>
                                <td style="color: {{ $data['Net_Earning'] < 0 ? 'red' : 'green' }}">
                                    {{ number_format($data['Net_Earning']) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    {{-- Grand Total for Second Table (Financial Summary) --}}
    @if(count($report) > 0)
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr style="font-weight:bold; background:#e0e0e0;">
                        <td style="width: 13%">Grand Total</td>
                        <td style="width: 9%">{{ number_format($grandTotal['Salary']) }}</td>
                        <td style="width:5%">{{ number_format($grandTotal['Maintenance'] ?? 0) }}</td>
                        <td style="width:5%">{{ number_format($grandTotal['Inventory'] ?? 0) }}</td>
                        <td style="width: 8%">{{ number_format($grandTotal['Total_Exp']) }}</td>
                        <td style="width: 14%">{{ number_format($grandTotal['Sale_Rent']) }}</td>
                        <td style="width: 14%">{{ number_format($grandTotal['Gross_Earning']) }}</td>
                        <td style="width: 13%; color: {{ $grandTotal['Net_Earning'] < 0 ? 'red' : 'green' }}">
                            {{ number_format($grandTotal['Net_Earning']) }}
                        </td>
                    </tr>

                    {{-- Category-wise Totals for Financial Summary --}}
                    @foreach($categoryTotals as $category => $totals)
                        @if(strtolower($category) == 'trailers')
                            <tr style="background:#f9f9f9;">
                                <td>{{ $category }}</td>
                                <td>{{ number_format($totals['Salary']) }}</td>
                                <td>{{ number_format($totals['Maintenance'] ?? 0) }}</td>
                                <td>{{ number_format($totals['Inventory'] ?? 0) }}</td>
                                <td>{{ number_format($totals['Total_Exp']) }}</td>
                                <td>{{ number_format($totals['Sale_Rent']) }}</td>
                                <td>{{ number_format($totals['Gross_Earning']) }}</td>
                                <td style="color: {{ $totals['Net_Earning'] < 0 ? 'red' : 'green' }}">
                                    {{ number_format($totals['Net_Earning']) }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>

@endsection
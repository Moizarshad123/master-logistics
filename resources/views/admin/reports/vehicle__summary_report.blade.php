@extends('admin.layouts.app')
@section('title', 'Vehicle Summary Report')

@section('content')

<div class="content">
    <h3 class="mb-3">Vehicle Summary Report</h3>

    <form method="GET" action="{{ route('admin.vehicleSummaryReport') }}" class="mb-4">
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

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.vehicleSummaryReport') }}" class="btn btn-secondary ms-2">
                    Reset
                </a>
            </div>
        </div>
    </form>

    {{-- FIRST TABLE: Expenses (Vehicle No to Brokerage) - NO ADVANCE HERE --}}
    @foreach($report as $category => $vehicles)
        <h4>{{ $category }}</h4>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Vehicle No</th>
                        <th>Trips</th>
                        <th>Total Journey</th>
                        <th>Meal</th>
                        <th>Fueling</th>
                        <th>Service</th>
                        <th>Route</th>
                        <th>Toll Tax</th>
                        <th>Tyre Punc/Air</th>
                        <th>Labor</th>
                        <th>Repair</th>
                        <th>Misc</th>
                        <th>Brokerage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehicles as $vehicleNo => $data)
                        <tr>
                            <td>{{ $vehicleNo }}</td>
                            <td>{{ $data['trips'] }}</td>
                            <td>{{ $data['total_journeys'] }}</td>
                            <td>{{ number_format($data['Meal']) }}</td>
                            <td>{{ number_format($data['Fueling']) }}</td>
                            <td>{{ number_format($data['Service']) }}</td>
                            <td>{{ number_format($data['Route']) }}</td>
                            <td>{{ number_format($data['Toll Tax']) }}</td>
                            <td>{{ number_format($data['Tyre Punc/Air']) }}</td>
                            <td>{{ number_format($data['Labor']) }}</td>
                            <td>{{ number_format($data['Repair']) }}</td>
                            <td>{{ number_format($data['Misc']) }}</td>
                            <td>{{ number_format($data['Brokerage']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- Grand Total for First Table (Expenses) --}}
    <div class="table-responsive">
        <table class="table table-bordered">
            <tbody>
                <tr style="font-weight:bold; background:#e0e0e0;">
                    <td style="width: 9%">Grand Total</td>
                    <td style="width: 5%">{{ $grandTotal['trips'] }}</td>
                    <td style="width: 7%">{{ $grandTotal['total_journeys'] }}</td>
                    <td style="width: 7%">{{ number_format($grandTotal['Meal']) }}</td>
                    <td style="width: 7%">{{ number_format($grandTotal['Fueling']) }}</td>
                    <td style="width: 7%">{{ number_format($grandTotal['Service']) }}</td>
                    <td style="width: 7%">{{ number_format($grandTotal['Route']) }}</td>
                    <td style="width: 7%">{{ number_format($grandTotal['Toll Tax']) }}</td>
                    <td style="width: 7%">{{ number_format($grandTotal['Tyre Punc/Air']) }}</td>
                    <td style="width: 6%">{{ number_format($grandTotal['Labor']) }}</td>
                    <td style="width: 7%">{{ number_format($grandTotal['Repair']) }}</td>
                    <td style="width: 7%">{{ number_format($grandTotal['Misc']) }}</td>
                    <td style="width: 7%">{{ number_format($grandTotal['Brokerage']) }}</td>
                </tr>

                {{-- Category-wise Totals for Expenses --}}
                @foreach($categoryTotals as $category => $totals)
                    <tr style="background:#f9f9f9;">
                        <td>{{ $category }}</td>
                        <td>{{ $totals['trips'] }}</td>
                        <td>{{ $totals['total_journeys'] }}</td>
                        <td>{{ number_format($totals['Meal']) }}</td>
                        <td>{{ number_format($totals['Fueling']) }}</td>
                        <td>{{ number_format($totals['Service']) }}</td>
                        <td>{{ number_format($totals['Route']) }}</td>
                        <td>{{ number_format($totals['Toll Tax']) }}</td>
                        <td>{{ number_format($totals['Tyre Punc/Air']) }}</td>
                        <td>{{ number_format($totals['Labor']) }}</td>
                        <td>{{ number_format($totals['Repair']) }}</td>
                        <td>{{ number_format($totals['Misc']) }}</td>
                        <td>{{ number_format($totals['Brokerage']) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <br><br>

    {{-- SECOND TABLE: Financial Summary (Salary, Advance, Total Exp to Net Earning) --}}
    @foreach($report as $category => $vehicles)
        <h4>{{ $category }}</h4>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Vehicle No</th>
                        <th>Salary</th>
                        <th>Advance</th> {{-- NEW COLUMN --}}
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
                            <td>{{ number_format($data['Advance']) }}</td> {{-- NEW DATA --}}
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
    @endforeach

    {{-- Grand Total for Second Table (Financial Summary) --}}
    <div class="table-responsive">
        <table class="table table-bordered">
            <tbody>
                <tr style="font-weight:bold; background:#e0e0e0;">
                    <td style="width: 17%">Grand Total</td>
                    <td style="width: 14%">{{ number_format($grandTotal['Salary']) }}</td>
                    <td style="width: 14%">{{ number_format($grandTotal['Advance']) }}</td> {{-- NEW --}}
                    <td style="width: 14%">{{ number_format($grandTotal['Total_Exp']) }}</td>
                    <td style="width: 14%">{{ number_format($grandTotal['Sale_Rent']) }}</td>
                    <td style="width: 14%">{{ number_format($grandTotal['Gross_Earning']) }}</td>
                    <td style="width: 13%; color: {{ $grandTotal['Net_Earning'] < 0 ? 'red' : 'green' }}">
                        {{ number_format($grandTotal['Net_Earning']) }}
                    </td>
                </tr>

                {{-- Category-wise Totals for Financial Summary --}}
                @foreach($categoryTotals as $category => $totals)
                    <tr style="background:#f9f9f9;">
                        <td>{{ $category }}</td>
                        <td>{{ number_format($totals['Salary']) }}</td>
                        <td>{{ number_format($totals['Advance']) }}</td> {{-- NEW --}}
                        <td>{{ number_format($totals['Total_Exp']) }}</td>
                        <td>{{ number_format($totals['Sale_Rent']) }}</td>
                        <td>{{ number_format($totals['Gross_Earning']) }}</td>
                        <td style="color: {{ $totals['Net_Earning'] < 0 ? 'red' : 'green' }}">
                            {{ number_format($totals['Net_Earning']) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

@endsection
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


    @foreach($report as $category => $vehicles)
        <h4>{{ $category }}</h4>

        <table class="table">
            <tr>
                <th>Vehicle No</th>
                <th>Trips</th>
                <th>Meal</th>
                <th>Fueling</th>
                <th>Service</th>
                <th>Route</th>
                <th>Toll Tax</th>
                <th>Tyre Punc/Air</th>
                <th>Labor</th>
                <th>Repair</th>
                <th>Misc</th>
            </tr>

            @foreach($vehicles as $vehicleNo => $data)
                <tr>
                    <td>{{ $vehicleNo }}</td>
                    <td>{{ $data['trips'] }}</td>
                    <td>{{ number_format($data['Meal']) }}</td>
                    <td>{{ number_format($data['Fueling']) }}</td>
                    <td>{{ number_format($data['Service']) }}</td>
                    <td>{{ number_format($data['Route']) }}</td>
                    <td>{{ number_format($data['Toll Tax']) }}</td>
                    <td>{{ number_format($data['Tyre Punc/Air']) }}</td>
                    <td>{{ number_format($data['Labor']) }}</td>
                    <td>{{ number_format($data['Repair']) }}</td>
                    <td>{{ number_format($data['Misc']) }}</td>
                </tr>
            @endforeach
        </table>
    @endforeach

        <table class="table" border="1">
            <tr style="font-weight:bold; background:#f2f2f2;">
                <td style="width: 10%">Grand Total</td>
                <td>{{ $grandTotal['trips'] }}</td>
                <td>{{ number_format($grandTotal['Meal']) }}</td>
                <td>{{ number_format($grandTotal['Fueling']) }}</td>
                <td>{{ number_format($grandTotal['Service']) }}</td>
                <td>{{ number_format($grandTotal['Route']) }}</td>
                <td>{{ number_format($grandTotal['Toll Tax']) }}</td>
                <td>{{ number_format($grandTotal['Tyre Punc/Air']) }}</td>
                <td>{{ number_format($grandTotal['Labor']) }}</td>
                <td>{{ number_format($grandTotal['Repair']) }}</td>
                <td>{{ number_format($grandTotal['Misc']) }}</td>
            </tr>
        </table>

</div>

@endsection

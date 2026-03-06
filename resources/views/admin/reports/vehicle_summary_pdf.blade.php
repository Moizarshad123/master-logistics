<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Vehicle Summary Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2,
        h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th {
            background: #f0f0f0;
            padding: 6px;
            text-align: center;
            font-size: 8px
        }

        td {
            padding: 5px;
            text-align: center;
            font-size: 8px;

        }

        .negative {
            color: red;
        }

        .positive {
            color: green;
        }

        .total-row {
            font-weight: bold;
            background: #e0e0e0;
        }

    </style>
</head>

<body>

    <h2>Vehicle Summary Report</h2>

    @if(request('from_date'))
    <p>
        From: {{ request('from_date') }} |
        To: {{ request('to_date') }}
    </p>
    @endif


    {{-- EXPENSE TABLE --}}
    @foreach($report as $category => $vehicles)

    @if(strtolower($category) !== 'trailers')

    <h3>{{ $category }}</h3>

    <table>
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

                <th>Maintenance</th>
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

                <td>{{ number_format($data['Maintenance'] ?? 0) }}</td>
                <td>{{ number_format($data['Inventory'] ?? 0) }}</td>
            </tr>

            @endforeach

        </tbody>
    </table>

    @endif
    @endforeach


    {{-- FINANCIAL SUMMARY --}}

    @foreach($report as $category => $vehicles)

    @if(strtolower($category) !== 'trailers')

    <h3>{{ $category }} - Financial Summary</h3>

    <table>
        <thead>
            <tr>
                <th>Vehicle</th>
                <th>Salary</th>
                <th>Advance</th>
                <th>Maintenance</th>
                <th>Inventory</th>
                <th>Total Exp</th>
                <th>Sale Rent</th>
                <th>Gross</th>
                <th>Net</th>
            </tr>
        </thead>

        <tbody>

            @foreach($vehicles as $vehicleNo => $data)

            <tr>

                <td>{{ $vehicleNo }}</td>

                <td>{{ number_format($data['Salary']) }}</td>

                <td>{{ number_format($data['Advance']) }}</td>

                <td>{{ number_format($data['Maintenance'] ?? 0) }}</td>

                <td>{{ number_format($data['Inventory'] ?? 0) }}</td>

                <td>{{ number_format($data['Total_Exp']) }}</td>

                <td>{{ number_format($data['Sale_Rent']) }}</td>

                <td>{{ number_format($data['Gross_Earning']) }}</td>

                <td class="{{ $data['Net_Earning'] < 0 ? 'negative':'positive' }}">
                    {{ number_format($data['Net_Earning']) }}
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

    @endif
    @endforeach


    {{-- GRAND TOTAL --}}

    <table>

        <tr class="total-row">
            <td>Grand Total Salary</td>
            <td>{{ number_format($grandTotal['Salary']) }}</td>
        </tr>

        <tr class="total-row">
            <td>Grand Total Advance</td>
            <td>{{ number_format($grandTotal['Advance']) }}</td>
        </tr>

        <tr class="total-row">
            <td>Total Expenses</td>
            <td>{{ number_format($grandTotal['Total_Exp']) }}</td>
        </tr>

        <tr class="total-row">
            <td>Total Sale Rent</td>
            <td>{{ number_format($grandTotal['Sale_Rent']) }}</td>
        </tr>

        <tr class="total-row">
            <td>Net Profit</td>
            <td>{{ number_format($grandTotal['Net_Earning']) }}</td>
        </tr>

    </table>


</body>

</html>

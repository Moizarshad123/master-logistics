@extends('admin.layouts.app')
@section('title', 'Payroll Report')

@section('content')
<div class="container">

    <h3>Payroll Report ({{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }})</h3>

    {{-- FILTER --}}
    <form method="GET" class="row mb-3">
        <div class="col-md-2">
            <select name="month" class="form-control">
                @for($m=1;$m<=12;$m++)
                    <option value="{{ $m }}" @selected($month==$m)>
                        {{ date('F', mktime(0,0,0,$m,1)) }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <input type="number" name="year" value="{{ $year }}" class="form-control">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary">Generate Report</button>
        </div>
         <div class="col-md-2">
            <a href="{{ route('admin.deductLoan') }}" class="btn btn-warning">Deduct Loan</a>
        </div>

        

        <div class="col-md-4 text-end">
            <a href="{{ route('admin.payroll.pdf',['month'=>$month,'year'=>$year]) }}"
               class="btn btn-success">
               Download PDF
            </a>
        </div>
    </form>

    {{-- REPORT TABLE --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style="text-align: center">EMP: ID#</th>
                <th>Driver</th>
                <th>Total Days</th>
                <th>Present</th>
                <th>Absent</th>
                <th>Leave</th>
                <th>Gross Salary</th>
                <th>Deduction</th>
                <th>Advance Salary</th>
                <th>Loan Amount</th>
                <th>Net Salary</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $row)
                <tr>
                    <td style="text-align: center">{{ $row['driver']->emp_id }}</td>
                    <td>{{ $row['driver']->name }}</td>
                    <td>{{ $row['totalDays'] }}</td>
                    <td>{{ $row['present'] }}</td>
                    <td>{{ $row['absent'] }}</td>
                    <td>{{ $row['leave'] }}</td>
                    <td>{{ number_format($row['grossSalary']) }}</td>
                    <td>{{ number_format($row['deduction']) }}</td>
                    <td>{{ $row["advance"] ?? 0}}</td>
                    <td>{{ $row["loanDeduction"] ?? 0}}</td>
                    <td><strong>{{ number_format($row['netSalary']) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection

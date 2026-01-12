<!DOCTYPE html>
<html>
<head>
    <style>
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:6px; }
        body {
            font-size: 13px
        }
    </style>
</head>
<body>

<h3>Payroll Report - {{ date('F', mktime(0,0,0,$month,1)) }} {{ $year }}</h3>

<table>
    <thead>
        <tr>
            <th>Driver</th>
            <th>Total Days</th>
            <th>Present</th>
            <th>Absent</th>
            <th>Leave</th>
            <th>Gross Salary</th>
            <th>Deduction</th>
            <th>Net Salary</th>
        </tr>
    </thead>
    <tbody>
        @foreach($report as $row)
        <tr>
            <th>{{ $row['driver']->name }}</th>
            <td>{{ $row['totalDays'] }}</td>
            <td>{{ $row['present'] }}</td>
            <td>{{ $row['absent'] }}</td>
            <td>{{ $row['leave'] }}</td>
            <td>{{ number_format($row['grossSalary']) }}</td>
            <td>{{ number_format($row['deduction']) }}</td>
            <td>{{ number_format($row['netSalary']) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>

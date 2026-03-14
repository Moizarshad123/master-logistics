@extends('admin.layouts.app')
@section('title', 'Monthly Attendance')

@section('content')
<div class="container">
    <h3>Monthly Attendance</h3>

    {{-- Month Filter (GET) --}}
    <form method="GET" action="{{ url('admin/attendance') }}" class="row mb-3">
        <div class="col-md-3">
            <label>Select Month</label>
            <input type="month" name="month" class="form-control"
                   value="{{ $selectedMonth }}"
                   onchange="this.form.submit()">
        </div>
    </form>

    {{-- Attendance Save (POST) --}}
    <form method="POST" action="{{ url('admin/attendance') }}">
        @csrf
        <input type="hidden" name="month" value="{{ $selectedMonth }}">

        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Driver</th>
                    <th>Present Days</th>
                    <th>Absent Days</th>
                    <th>Leave Days</th>
                </tr>
            </thead>
            <tbody>
                @foreach($drivers as $driver)
                @php
                    $att = $attendances[$driver->id] ?? null;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $driver->name }}</td>
                    <td>
                        <input type="number"
                               name="attendance[{{ $driver->id }}][present_days]"
                               class="form-control"
                               min="0" max="31"
                               step="0.01"
                               value="{{ $att->present_days ?? 0 }}"
                               style="width: 80px;">
                    </td>
                    <td>
                        <input type="number"
                               name="attendance[{{ $driver->id }}][absent_days]"
                               class="form-control"
                               min="0" max="31"
                               step="0.01"

                               value="{{ $att->absent_days ?? 0 }}"
                               style="width: 80px;">
                    </td>
                    <td>
                        <input type="number"
                               name="attendance[{{ $driver->id }}][leave_days]"
                               class="form-control"
                               min="0" max="31"
                               step="0.01"

                               value="{{ $att->leave_days ?? 0 }}"
                               style="width: 80px;">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="submit" class="btn btn-success">
            Save Attendance
        </button>
    </form>
</div>
@endsection
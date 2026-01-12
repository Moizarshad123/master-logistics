@extends('admin.layouts.app')
@section('title', 'Daily Attendance')

@section('content')
<div class="container">
    <h3>Daily Attendance</h3>

    <form method="POST" action="{{ url('admin/attendance') }}">
        @csrf

        <div class="row mb-3">
            <div class="col-md-4">
                <label>Date</label>
                <input type="date" name="date" class="form-control" required value="{{date('Y-m-d')}}">
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Driver</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Leave</th>
                </tr>
            </thead>
            <tbody>
                @foreach($drivers as $driver)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $driver->name }}</td>
                    <td>
                        <input type="radio"
                               name="attendance[{{ $driver->id }}]"
                               value="present"
                               checked>
                    </td>
                    <td>
                        <input type="radio"
                               name="attendance[{{ $driver->id }}]"
                               value="absent">
                    </td>
                    <td>
                        <input type="radio"
                               name="attendance[{{ $driver->id }}]"
                               value="leave">
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button class="btn btn-primary">
            Save Attendance
        </button>
    </form>
</div>
@endsection

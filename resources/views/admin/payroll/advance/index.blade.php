@extends('admin.layouts.app')
@section('title', 'Advance Salaries')
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
@endsection
@section('content')
<div class="container">

    <div class="row mb-3">
        <div class="col-md-10">
            <h3>Advance Salaries</h3>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.advance-salaries.create') }}" class="btn btn-success btn-sm">+ Add Advance Salary</a>
        </div>
    </div>

    <h3></h3>

    {{-- REPORT TABLE --}}
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Driver</th>
                <th>Advance Month & Year</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($advance as $row)
            <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row['driver']->name }}</td>
                <td>{{ $row['month'] .' - '.$row['year'] }}</td>
                <td>{{ $row['amount'] }}</td>
                <th>
                    @if($row->status == "Amount Due")
                    <span class="badge badge-warning">{{ $row->status }}</span>

                    @else
                    <span class="badge badge-success">{{ $row->status}}</span>
                    @endif
                </th>
                <td>
                   <a href="{{ route('admin.advance-salaries.edit', $row->id) }}" class="btn btn-sm btn-primary">Edit</a> 
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection

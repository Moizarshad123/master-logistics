@extends('admin.layouts.app')
@section('title', 'Maintenances')

@section('content')

  <div class="mb-4">
        <div class="row">
            <div class="col-md-8">
                <h3>Maintenances</h3>
                
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.maintenances.create') }}" class="btn btn-sm btn-success">+Add Maintenance</a>
            </div>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vehicle No</th>
                <th>Expense Type</th>
                <th>Amount</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($maintenances as $m)
                <tr>
                    <td>{{ $m->id }}</td>
                    <td>{{ $m->vehicle->vehicle_no ?? '' }}</td>
                    <td>{{ $m->expense->name ?? '' }}</td>
                    <td>{{ $m->amount }}</td>
                    <td>{{ $m->comments }}</td>
                    <td>
                        <a href="{{ route('admin.maintenances.edit', $m) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.maintenances.destroy', $m) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this maintenance?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

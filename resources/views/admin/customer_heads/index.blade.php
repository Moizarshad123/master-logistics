@extends('admin.layouts.app')
@section('title', 'Customer Heads')

@section('content')

    <div class="row">
        <div class="col-md-10">
            <h3>Customer Heads</h3>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.customer-heads.create') }}" class="btn btn-sm btn-success">+Add Customer Head</a>
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customerHeads as $head)
                <tr>
                    <td>{{ $head->id }}</td>
                    <td>{{ $head->name }}</td>
                    <td>
                        <a href="{{ route('admin.customer-heads.edit', $head->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.customer-heads.destroy', $head->id) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this Customer Head?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

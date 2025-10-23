@extends('admin.layouts.app')
@section('title', 'Expense Categories')

@section('content')

<div class="mb-4">
    <div class="row">
        <div class="col-md-10">
            <h3>Expense Categories</h3>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.expense-categories.create') }}" class="btn btn-primary mb-3">+ Add New</a>
        </div>
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
        @forelse($categories as $category)
        <tr>
            <td>{{ $category->id }}</td>
            <td>{{ $category->name }}</td>
            <td>
                <a href="{{ route('admin.expense-categories.edit', $category->id) }}"
                    class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('admin.expense-categories.destroy', $category->id) }}" method="POST"
                    style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete this category?')">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3">No categories found.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection

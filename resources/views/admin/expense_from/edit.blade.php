@extends('admin.layouts.app')
@section('title', 'Update Expense From')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Update Expense From</h3>
    <form action="{{ route('admin.expense-from.update', $expenseFrom->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group mb-3">
            <label>Name</label>
            <input type="text" name="name" value="{{ $expenseFrom->name }}" class="form-control" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.expense-from.index') }}" class="btn btn-secondary">Cancel</a>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')
 
@endsection

@extends('admin.layouts.app')
@section('title', 'Edit Expense Type')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Edit Expense Type</h3>
      <form action="{{ route('admin.expense-types.update', $expenseType) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Expense Type Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $expenseType->name) }}" required>
            @error('name')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="btn btn-primary mt-2">Update</button>
        <a href="{{ route('admin.expense-types.index') }}" class="btn btn-secondary mt-2">Back</a>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')

<script>

</script>
@endsection

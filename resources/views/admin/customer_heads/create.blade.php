@extends('admin.layouts.app')
@section('title', 'Add Customer Head')

@section('content')
<div class="container">
    <h2>Add Customer Head</h2>

    <form action="{{ route('admin.customer-heads.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name:</label>
            <input type="text" name="name" class="form-control" required maxlength="150">
        </div>
        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection

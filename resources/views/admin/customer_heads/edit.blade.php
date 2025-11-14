@extends('admin.layouts.app')
@section('title', 'Edit Customer Heads')

@section('content')
<div class="container">
    <h2>Edit Customer Head</h2>

    <form action="{{ route('admin.customer-heads.update', $customerHead->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Name:</label>
            <input type="text" name="name" value="{{ $customerHead->name }}" class="form-control" required>
        </div>
        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection

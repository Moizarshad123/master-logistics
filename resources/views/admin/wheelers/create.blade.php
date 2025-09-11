@extends('admin.layouts.app')
@section('title', 'Add Wheeler')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Add Wheeler</h3>
    <form action="{{ route('admin.wheelers.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <button class="btn btn-success">Save</button>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')

<script>

</script>
@endsection

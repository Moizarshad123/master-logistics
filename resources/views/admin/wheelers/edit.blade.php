@extends('admin.layouts.app')
@section('title', 'Add Wheeler')

@section('css')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Edit Wheeler</h3>
    <form action="{{ route('admin.wheelers.update', $wheeler) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $wheeler->name }}" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <button class="btn btn-success">Update</button>
    </form>
</div>
@endsection

@section('js')

<script>

</script>
@endsection

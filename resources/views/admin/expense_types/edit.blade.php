@extends('admin.layouts.app')
@section('title', 'Edit Expense Type')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Edit Expense Type</h3>
      <form action="{{ route('admin.expense-types.update', $expenseType) }}" method="POST" id="expenseTypeForm">
        @csrf
        @method('PUT')
         <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label>Expense Category</label>
                    <select name="category_id" id="category_id" class="form-select">
                        <option value="">Select Category</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}" {{ $item->id == $expenseType->category_id ? "selected" : "" }}>{{ $item->name}}</option>
                        @endforeach
                    </select>
                    @error('category_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Expense Type Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $expenseType->name) }}" required>
                    @error('name')<span class="text-danger">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success mt-2" id="addExpenseType">Update</button>
        <a href="{{ route('admin.expense-types.index') }}" class="btn btn-secondary mt-2">Back</a>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('#addExpenseType').on('click', function (e) {

            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to update this expnese?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                  $('#expenseTypeForm').submit();
                }
            });
        });
    });

</script>
@endsection

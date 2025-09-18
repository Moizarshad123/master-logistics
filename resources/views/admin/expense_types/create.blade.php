@extends('admin.layouts.app')
@section('title', 'Add Expense Type')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Add Expense Type</h3>
     <form action="{{ route('admin.expense-types.store') }}" method="POST" id="expenseTypeForm">
        @csrf
        <div class="form-group">
            <label>Expense Type Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <button type="submit" class="btn btn-success mt-2" id="addExpenseType">Save</button>
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
                text: "Do you really want to add this expnese?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, add it!'
            }).then((result) => {
                if (result.isConfirmed) {
                  $('#expenseTypeForm').submit();
                }
            });
        });
    });

</script>
@endsection

@extends('admin.layouts.app')
@section('title', 'Add Wheeler')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Add Wheeler</h3>
    <form action="{{ route('admin.wheelers.store') }}" method="POST" id="expenseTypeForm">
        @csrf
        <div class="form-group mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <button class="btn btn-success" id="addExpenseType">Save</button>
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
                    text: "Do you really want to add this wheeler?",
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

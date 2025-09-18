@extends('admin.layouts.app')
@section('title', 'Add Vehicle')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Add Vehicle</h3>
     <form action="{{ route('admin.vehicles.store') }}" method="POST" enctype="multipart/form-data" id="expenseTypeForm">
        @csrf
        @include('admin.vehicles.form')
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary mt-2">Back</a>
        <button type="submit" class="btn btn-success mt-2" id="addExpenseType">Save</button>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.querySelectorAll('.image-input').forEach(input => {
            input.addEventListener('change', function(e) {
                let previewId = this.getAttribute('data-preview');
                let previewImg = document.getElementById(previewId);
                if (this.files && this.files[0]) {
                    let reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewImg.style.display = 'block';
                    }
                    reader.readAsDataURL(this.files[0]);
                } else {
                    previewImg.src = '';
                    previewImg.style.display = 'none';
                }
            });
        });

        $(document).ready(function() {
            $('#expense_types').select2({
                placeholder: "Select Expenses",
                allowClear: true
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#addExpenseType').on('click', function (e) {

                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to add this vehicle?",
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

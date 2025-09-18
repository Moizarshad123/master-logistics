@extends('admin.layouts.app')
@section('title', 'Add Driver')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Add Driver</h3>
    <form action="{{ route('admin.drivers.store') }}" method="POST" enctype="multipart/form-data" id="expenseTypeForm">
        @csrf
        @include('admin.drivers.form')
        <button type="submit" class="btn btn-primary" id="addExpenseType">Save</button>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')

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
    $(document).ready(function () {
        $('#addExpenseType').on('click', function (e) {

            e.preventDefault();

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to add this driver?",
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

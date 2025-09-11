@extends('admin.layouts.app')
@section('title', 'Edit Vehicle')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h3>Edit Vehicle</h3>
    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle->id) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        @include('admin.vehicles.form')
        <button class="btn btn-primary">Update</button>
    </form>
</div>
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
@endsection

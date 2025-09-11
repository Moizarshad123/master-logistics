@extends('admin.layouts.app')
@section('title', 'Edit Driver')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Edit Driver</h3>
    <form action="{{ route('admin.drivers.update', $driver->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.drivers.form', ['driver' => $driver])
        <button type="submit" class="btn btn-success">Update</button>
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
</script>
@endsection

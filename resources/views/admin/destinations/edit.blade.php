@extends('admin.layouts.app')
@section('title', 'Update Destination')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Update Destination</h3>
    <form action="{{ route('admin.destinations.update', $destination->id) }}" method="POST" enctype="multipart/form-data" id="expenseTypeForm">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-8">
                <label>Name:</label>
                <input type="text" name="name" value="{{ old('name', $destination->name) }}" class="form-control">
                @error('name') <p style="color:red">{{ $message }}</p> @enderror
            </div>
            <div class="col-md-4">
                <button type="submit"  style="margin-top: 25px;" class="btn btn-success" id="addExpenseType">Update</button>
            </div>
        </div>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')
 <script>
        $(document).ready(function () {
            // Add new row
            $("#addRow").click(function (e) {
                e.preventDefault();
                let row = `
                    <tr>
                        <td>
                            
                        </td>
                        <td class="text-center">
                            <button type="button" style="border-radius: 25%;" class="btn btn-danger btn-sm removeRow">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>`;

                $("#tripDetailsContainer").append(row);
            });

            // Remove row
            $(document).on("click", ".removeRow", function () {
                $(this).closest("tr").remove();
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#addExpenseType').on('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to update this destinations?",
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

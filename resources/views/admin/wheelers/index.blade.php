@extends('admin.layouts.app')
@section('title', 'Wheelers Types')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
@endsection

@section('content')
  <!-- content -->
  <div class="content ">

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Wheelers</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.wheelers.create') }}" class="btn btn-sm btn-success">Add Wheeler</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="ordersTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Wheeler</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($wheelers as $wheeler)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $wheeler->name }}</td>
                        <td>
                            <a href="{{ route('admin.wheelers.edit', $wheeler) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('admin.wheelers.destroy', $wheeler) }}" method="POST" style="display:inline-block;">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm deleteExpenseType">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
<!-- ./ content -->
@endsection


@section('js')
    <script>
        $(document).ready(function () {
            $('.deleteExpenseType').on('click', function (e) {
                e.preventDefault();
                const form = $(this).closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you really want to delete this wheeler?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

    </script>
@endsection
@extends('admin.layouts.app')
@section('title', 'Drivers')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    crossorigin="anonymous">
@endsection

@section('content')
<!-- content -->
<div class="content ">

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Drivers</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.drivers.create') }}" class="btn btn-sm btn-success">Add Driver</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="ordersTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>CNIC Front</th>
                    <th>CNIC Back</th>
                    <th>DL Front</th>
                    <th>DL Back</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($drivers as $driver)
                <tr>
                    <td>
                        @if($driver->image)
                            <img src="{{ $driver->image}}" width="80" height="80" class="rounded">
                        @endif
                    </td>
                    <td>{{ $driver->name }}</td>
                    <td>{{ $driver->phone }}</td>
                    <td>{{ $driver->address }}</td>
                    <td>
                        @if($driver->cnic_front)
                            <img src="{{ $driver->cnic_front}}" width="80" height="80" class="rounded">
                        @endif
                    </td>
                    <td>
                        @if($driver->cnic_back)
                            <img src="{{ $driver->cnic_back}}" width="80" height="80" class="rounded">
                        @endif
                    </td>
                    <td>
                        @if($driver->driving_license_front)
                            <img src="{{ $driver->driving_license_front}}" width="80" height="80" class="rounded">
                        @endif
                    </td>
                    <td>
                        @if($driver->driving_license_back)
                            <img src="{{ $driver->driving_license_back}}" width="80" height="80" class="rounded">
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.drivers.edit', $driver) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.drivers.destroy', $driver) }}" method="POST"
                            style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm deleteExpenseType">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan="9">
                        <p class="text-center">No Drivers Found</p>
                    </th>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="float:right">
            {{ $drivers->links() }}
        </div>
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
                text: "Do you really want to delete this driver?",
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

@extends('admin.layouts.app')
@section('title', 'Destinations')

@section('css')

@endsection

@section('content')
<!-- content -->

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Destinations</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.destinations.create') }}" class="btn btn-sm btn-success">+Add Destination</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="ordersTable">
            <thead>
                <tr>
                    <th>Destination Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($destinations as $item)
                <tr>

                    <td>{{ $item->name }}</td>
                    <td>
                        <a href="{{ route('admin.destinations.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.destinations.destroy', $item) }}" method="POST"  style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm deleteExpenseType">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan="2">
                        <p class="text-center">No Destinations Found</p>
                    </th>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="float:right">
            {{ $destinations->links() }}
        </div>
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('.deleteExpenseType').on('click', function (e) {

            e.preventDefault();
            const form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this destination?",
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

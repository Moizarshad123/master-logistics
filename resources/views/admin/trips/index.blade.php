@extends('admin.layouts.app')
@section('title', 'Trips')

@section('content')

<div class="content">
    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Trips</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.trips.create') }}" class="btn btn-sm btn-success">+ Add Trip</a>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="ordersTable">
            <thead>
                <tr>
                    <th>Trip No</th>
                    <th>Trip Type</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Total Journeys</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($trips as $trip)
                    <tr>
                        <td>{{ $trip->trip_no }}</td>
                        <td>{{ $trip->trip_type }}</td>
                        <td>{{ $trip->vehicle->vehicle_no ?? '-' }}</td>
                        <td>{{ $trip->driver->name ?? '-' }}</td>
                        <td>{{ $trip->tripDetails->count() }}</td>
                        <td>{{ $trip->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.trips.show', $trip->id) }}" class="btn btn-sm btn-info">View</a>
                            <a href="{{ route('admin.trips.edit', $trip->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.trips.destroy', $trip->id) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger deleteExpenseType">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    {{ $trips->links() }}
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
                text: "Do you really want to delete this trip?",
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

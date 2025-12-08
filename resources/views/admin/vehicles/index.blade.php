@extends('admin.layouts.app')
@section('title', 'Vehicles')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
@endsection

@section('content')
  <!-- content -->
  <div class="content ">
    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Vehicle</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.vehicles.create') }}" class="btn btn-sm btn-success">Add Vehicle</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="maintenanceTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Vehicle No</th>
                    <th>Wheeler Type</th>
                    <th>Chachis No</th>
                    <th>Engine No</th>
                    {{-- <th>Make</th>
                    <th>Model</th> --}}
                    <th style="text-align: center">Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- @forelse ($vehicles as $vehicle)
                    <tr>
                        <td>
                            @if($vehicle->image)
                                <img src="{{ $vehicle->image }}" width="80">
                            @endif
                        </td>
                        <td>{{ $vehicle->vehicle_no }}</td>
                        <td>{{ $vehicle?->wheeler?->name ?? "" }}</td>
                        <td>{{ $vehicle->chachis_no }}</td>
                        <td>{{ $vehicle->engine_no }}</td>
                     
                        <td style="text-align: center">
                            <a href="{{ route('admin.vehicles.edit', $vehicle->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.vehicles.destroy', $vehicle->id) }}" method="POST" style="display:inline-block;">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger deleteExpenseType">Delete</button>
                            </form>
                            <a href="{{ route('admin.vehicles.expenses', $vehicle->id) }}" class="btn btn-sm btn-info">View Expenses</a>

                        </td>
                    </tr>
                @empty
                <tr>
                    <th colspan="5">
                        <p class="text-center">No Vehicle</p>
                    </th>
                </tr>
                @endforelse --}}
            </tbody>
        </table>
        {{-- <div style="float:right">
            {{ $vehicles->links() }}
        </div> --}}
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
                text: "Do you really want to delete this vehicle?",
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

        var DataTable = $("#maintenanceTable").DataTable({
            buttons: [{
                extend: "csv",
                className: "btn-sm"
            }],
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: `{{route('admin.vehicles.index')}}`,
            },
            dom: '<"top d-flex justify-content-between"f p>rt<"bottom"p>',


            columns: [
                {
                    data: 'vehicleImage',
                    name: 'vehicleImage'
                },
                {
                    data: 'vehicle_no',
                    name: 'vehicle_no'
                },
                {
                    data: 'wheeler',
                    name: 'wheeler'
                },
                {
                    data: 'chachis_no',
                    name: 'chachis_no'
                },
                {
                    data: 'engine_no',
                    name: 'engine_no'
                },
                {
                    data: 'action',
                    name: 'action'
                }
            ],
            order: [[0, 'desc']],

            createdRow: function(row, data, dataIndex) {
                // Check if order_nature is 'urgent'
                if (data.order_nature == 'urgent' && data.outstanding_amount == 0) {
                    $(row).css('background-color', 'rgb(253 136 136)');
                } if(data.order_nature == 'normal' && data.outstanding_amount != 0) {
                    $(row).css('background-color', 'rgb(191 204 181)');
                } else if(data.order_nature == 'urgent' && data.outstanding_amount != 0) {
                    $(row).css('background-color', 'rgb(241 240 129)');
                }
            }

        });
    });

</script>
@endsection
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
        <table class="table table-custom table-lg mb-0" id="driversTable">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Salary</th>
                    <th>CNIC Expiry Date</th>
                    <th>License Expiry Date</th>
                    {{-- <th>Address</th> --}}
                    {{-- <th>CNIC Front</th>
                    <th>CNIC Back</th>
                    <th>DL Front</th>
                    <th>DL Back</th> --}}
                    <th style="text-align: center">Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- @forelse ($drivers as $driver)
                    <tr>
                        <td>
                            @if($driver->image)
                                <img src="{{ $driver->image}}" width="100" height="100" style="border-radius: 50%">
                            @endif
                        </td>
                        <td>{{ $driver->name }}</td>
                        <td>{{ $driver->phone }}</td>
                        <td>{{ $driver->salary }}</td>
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
                            <a href="{{ route('admin.driverPayments', $driver->id) }}" class="btn btn-info btn-sm">View Payments</a>
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
                @endforelse --}}
            </tbody>
        </table>
    </div>


</div>
<!-- ./ content -->
@endsection

@section('js')
<script>
    $(document).ready(function () {

        var DataTable = $("#driversTable").DataTable({
            buttons: [{
                extend: "csv",
                className: "btn-sm"
            }],
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: `{{route('admin.drivers.index')}}`,
            },
            dom: '<"top"p>rt<"bottom"p><"clear">',
            columns: [

                {
                    data: 'myImage',
                    name: 'myImage'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'salary',
                    name: 'salary'
                },
                {
                    data: 'cnic_expiry_date',
                    name: 'cnic_expiry_date'
                },
                {
                    data: 'license_expiry_date',
                    name: 'license_expiry_date'
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

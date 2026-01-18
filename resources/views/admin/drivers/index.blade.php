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
                    <th>Status</th>
                    {{-- <th>Address</th> --}}
                    {{-- <th>CNIC Front</th>
                    <th>CNIC Back</th>
                    <th>DL Front</th>
                    <th>DL Back</th> --}}
                    <th style="text-align: center">Action</th>
                </tr>
            </thead>
            <tbody>
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
                    data: 'status',
                    name: 'status'
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

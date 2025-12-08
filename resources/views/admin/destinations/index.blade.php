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
        <table class="table table-custom table-lg mb-0" id="customersTable">
            <thead>
                <tr>
                    <th>Destination Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
       
    </div>
@endsection

@section('js')
<script>
    $(document).ready(function () {

         var DataTable = $("#customersTable").DataTable({
            buttons: [{
                extend: "csv",
                className: "btn-sm"
            }],
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: `{{route('admin.destinations.index')}}`,
            },
            dom: '<"top d-flex justify-content-between"f p>rt<"bottom"p>',
            columns: [

                {
                    data: 'name',
                    name: 'name'
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

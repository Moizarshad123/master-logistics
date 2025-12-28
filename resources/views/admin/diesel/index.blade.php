@extends('admin.layouts.app')
@section('title', 'Diesel')

@section("css")
<style>
    table td, table th {
        white-space: normal !important;
        word-wrap: break-word;
        max-width: 200px; /* You can adjust size */
    }

    td.comments-column {
        max-width: 300px; /* specific width for comments column */
    }
</style>
@endsection

@section('content')

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Diesel</h3>
                
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.diesel.create') }}" class="btn btn-sm btn-success">+Add Diesel</a>
            </div>
        </div>
    </div>

    <table class="table table-bordered" id="dieselTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vehicle</th>
                <th>Type</th>
                <th>Created By</th>
                <th>Source</th>
                <th>Date-TIme</th>
                <th>Total Litres</th>
                <th>Per Litre Amount</th>
                <th>Total Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
          
        </tbody>
    </table>
@endsection

@section('js')
<script>
    $(document).ready(function() {
        var DataTable = $("#dieselTable").DataTable({
            buttons: [{
                extend: "csv",
                className: "btn-sm"
            }],
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: `{{route('admin.diesel.index')}}`,
            },
            dom: '<"top d-flex justify-content-between"f p>rt<"bottom"p>',

            columns: [

                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'vehicle',
                    name: 'vehicle'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'createdBy',
                    name: 'createdBy'
                },
                {
                    data: 'source',
                    name: 'source'
                },
                {
                    data: 'dateTime',
                    name: 'dateTime'
                },
                {
                    data: 'litres',
                    name: 'litres'
                },
                {
                    data: 'per_litre_amount',
                    name: 'per_litre_amount'
                },
                {
                    data: 'total_amount',
                    name: 'total_amount'
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
        
        var delete_id;
        $(document, this).on('click', '.delete', function() {
            delete_id = $(this).data('id');
            $('#confirmModal').modal('show');
        });

        $(document).on('click', '#ok_delete', function() {
            $.ajax({
                type: "delete",
                url: "{{url('admin/orderBigDC')}}/"+delete_id,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#ok_delete').text('Deleting...');
                    $('#ok_delete').attr("disabled", true);
                },
                success: function(data) {
                    DataTable.ajax.reload();
                    $('#ok_delete').text('Delete');
                    $('#ok_delete').attr("disabled", false);
                    $('#confirmModal').modal('hide');
                    //   js_success(data);
                    if (data == 0) {
                        toastr.error("Tag Exist in Products");
                    } else if (data == 2) {
                        toastr.error("Tag Exist in Collections");
                    } else {
                        toastr.success('Record Deleted Successfully');
                    }
                }
            })
        });

        $('.deleteExpenseType').on('click', function (e) {

            e.preventDefault();
            const form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this diesel?",
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


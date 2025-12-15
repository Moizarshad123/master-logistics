@extends('admin.layouts.app')
@section('title', 'Maintenances')

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
                <h3>Maintenances</h3>
                
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.maintenances.create') }}" class="btn btn-sm btn-success">+Add Maintenance</a>
            </div>
        </div>
    </div>

    <table class="table table-bordered" id="maintenanceTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Vehicle No</th>
                <th>Expense Type</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {{-- @foreach($maintenances as $m)
                <tr>
                    <td>{{ $m->id }}</td>
                    <td>{{ $m->vehicle->vehicle_no ?? '' }}</td>
                    <td>{{ $m->expense->name ?? '' }}</td>
                    <td>{{ $m->amount }}</td>
                    <td>{{ date('d-m-Y',strtotime($m->created_at)) ?? "" }}</td>

                    <td class="comments-column">{{ $m->comments }}</td>
                    <td>
                        <a href="{{ route('admin.maintenances.edit', $m) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.maintenances.destroy', $m) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this maintenance?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach --}}
        </tbody>
    </table>
        {{-- <div style="float:right">
            {{ $maintenances->links() }}
        </div> --}}
@endsection

@section('js')
<script>
    $(document).ready(function() {
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
                url: `{{route('admin.maintenances.index')}}`,
            },
            dom: '<"top d-flex justify-content-between"f p>rt<"bottom"p>',

            columns: [

                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'vehicle_id',
                    name: 'vehicle_id'
                },
                {
                    data: 'expense',
                    name: 'expense',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'amount',
                    name: 'amount'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'comments',
                    name: 'comments'
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
    });
</script>
@endsection


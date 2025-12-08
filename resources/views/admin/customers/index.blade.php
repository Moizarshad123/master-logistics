@extends('admin.layouts.app')
@section('title', 'Customers')

@section('css')

@endsection

@section('content')
<!-- content -->

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Customers</h3>
                
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.customers.create') }}" class="btn btn-sm btn-success">+Add Customer</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="customersTable">
            <thead>
                <tr>
                    <th>Customer Head</th>
                    <th>Customer Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                {{-- @forelse ($customers as $item)
                    <tr>
                        <td>{{ $item->customerHead->name ?? '' }}</td>
                        <td>{{ $item->name }}</td>

                        <td>
                            <a href="{{ route('admin.customers.edit', $item) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('admin.customers.destroy', $item) }}" method="POST"  style="display:inline-block;">
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
                            <p class="text-center">No Customers Found</p>
                        </th>
                    </tr>
                @endforelse --}}
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
                url: `{{route('admin.customers.index')}}`,
            },
            dom: '<"top d-flex justify-content-between"f p>rt<"bottom"p>',
            columns: [

                {
                    data: 'customerHead',
                    name: 'customerHead'
                },
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
                text: "Do you really want to delete this customer?",
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

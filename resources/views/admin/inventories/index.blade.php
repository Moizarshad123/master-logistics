{{-- resources/views/admin/inventories/index.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Inventory')

@section('css')
@endsection

@section('content')

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Inventory</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.inventories.create') }}" class="btn btn-sm btn-success">Add Item</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="inventoryTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Item Name</th>
                    <th>Purchase Date</th>
                    <th>Unit Price</th>
                    <th>Qty</th>
                    <th>Total Price</th>
                    <th style="text-align: center">Action</th>
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

        var DataTable = $("#inventoryTable").DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: `{{ route('admin.inventories.index') }}`,
            },
            dom: '<"top d-flex justify-content-between"f p>rt<"bottom"p>',
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                { data: 'item_name',     name: 'item_name' },
                { data: 'purchase_date', name: 'purchase_date' },
                { data: 'unit_price',    name: 'unit_price' },
                { data: 'qty',           name: 'qty' },
                { data: 'price',         name: 'price' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [[0, 'desc']],
        });

    });
</script>
@endsection
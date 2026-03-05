{{-- resources/views/admin/inventory_items/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Inventory Items')

@section('css')
@endsection

@section('content')

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Inventory Items</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.inventory-items.create') }}" class="btn btn-sm btn-success">Add Item</a>
            </div>
        </div>
    </div>


    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="inventoryItemsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Price</th>
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

        var DataTable = $("#inventoryItemsTable").DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: `{{ route('admin.inventory-items.index') }}`,
            },
            dom: '<"top d-flex justify-content-between"f p>rt<"bottom"p>',
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                { data: 'name',  name: 'name' },
                { data: 'make',  name: 'make' },
                { data: 'model', name: 'model' },
                { data: 'price', name: 'price' },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            order: [[0, 'desc']],
        });

    });
</script>
@endsection
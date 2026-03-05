{{-- resources/views/admin/issuances/index.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Issuances')

@section('css')
@endsection

@section('content')

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Issuances</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.issuances.create') }}" class="btn btn-sm btn-success">Issue Item</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="issuancesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Vehicle</th>
                    <th>Item Name</th>
                    <th>Issued Qty</th>
                    <th>Remaining Qty</th>
                    <th>Issue Date</th>
                    <th style="text-align:center">Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

@endsection

@section('js')
<script>
    $(document).ready(function () {
        $("#issuancesTable").DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: `{{ route('admin.issuances.index') }}`,
            },
            dom: '<"top d-flex justify-content-between"f p>rt<"bottom"p>',
            columns: [
                { data: 'DT_RowIndex',    name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'vehicle_no',     name: 'vehicle_no',  searchable: false },
                { data: 'item_name',      name: 'item_name',   searchable: false },
                { data: 'qty',            name: 'qty' },
                { data: 'remaining_qty',  name: 'remaining_qty', searchable: false },
                { data: 'issue_date',     name: 'issue_date' },
                { data: 'action',         name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'desc']],
        });
    });
</script>
@endsection
{{-- resources/views/admin/overheads/index.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Overheads')

@section('css')
@endsection

@section('content')

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Overheads</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.overheads.create') }}" class="btn btn-sm btn-success">Add Overhead</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="overheadsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Expense Type</th>
                    <th>Driver</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Comment</th>
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

      
        $("#overheadsTable").DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: `{{ route('admin.overheads.index') }}`,
            },
            dom: '<"top d-flex justify-content-between"f p>rt<"bottom"p>',
            columns: [
                { data: 'DT_RowIndex',       name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'expense_type_name', name: 'expense_type_name', searchable: false },
                { data: 'driver_name',       name: 'driver_name', searchable: false },
                { data: 'amount',            name: 'amount' },
                { data: 'date',              name: 'date' },
                { data: 'comment',           name: 'comment', searchable: false },
                { data: 'action',            name: 'action', orderable: false, searchable: false },
            ],
            order: [[0, 'desc']],
        });
    });
</script>
@endsection
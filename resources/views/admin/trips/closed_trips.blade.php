@extends('admin.layouts.app')
@section('title', 'Closed Trips')

@section('content')

<div class="content">
    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Closed Trips</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.trips.create') }}" class="btn btn-sm btn-success">+ Add Trip</a>
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="tripsTable">
            <thead>
                <tr>
                    <th>Trip No</th>
                    <th>Total Journeys</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Trip Start Date</th>
                    <th>Trip End Date</th>
                    @if(auth()->user()->role_id == 1)
                    <th>Action</th>
                    @endif
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')

<script>
    $(document).ready(function() {
        // FIRST: Define columns array
        var columns = [
            {
                data: 'id',
                name: 'id'
            },
            {
                data: 'journey_count',
                name: 'journey_count'
            },
            {
                data: 'vehicle',
                name: 'vehicle'
            },
            {
                data: 'driver',
                name: 'driver'
            },
            {
                data: 'trip_date',
                name: 'trip_date'
            },
            {
                data: 'trip_end_date',
                name: 'trip_end_date'
            }
        ];

        // THEN: Conditionally add action column
        @if(auth()->user()->role_id == 1)
        columns.push({
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
        });
        @endif

        // FINALLY: Initialize DataTable with columns
        var DataTable = $("#tripsTable").DataTable({
            buttons: [{
                extend: "csv",
                className: "btn-sm"
            }],
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: `{{route('admin.closedTrips')}}`,
            },
            dom: '<"top d-flex justify-content-between"f p>rt<"bottom"p>',
            columns: columns,  // Use the columns array here
            order: [[0, 'desc']],
            createdRow: function(row, data, dataIndex) {
                // Check if order_nature is 'urgent'
                if (data.order_nature == 'urgent' && data.outstanding_amount == 0) {
                    $(row).css('background-color', 'rgb(253 136 136)');
                } else if(data.order_nature == 'normal' && data.outstanding_amount != 0) {
                    $(row).css('background-color', 'rgb(191 204 181)');
                } else if(data.order_nature == 'urgent' && data.outstanding_amount != 0) {
                    $(row).css('background-color', 'rgb(241 240 129)');
                }
            }
        });
    });
</script>

@endsection
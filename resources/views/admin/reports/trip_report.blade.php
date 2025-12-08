@extends('admin.layouts.app')
@section('title', 'Trip Vehicle Report')

@section('content')

<div class="content">
    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Trip Vehicle Report</h3>
            </div>
            <div class="col-md-2">
            </div>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="ordersTable">
            <thead>
                <tr>
                    <th>Trip No</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Total Journeys</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- @foreach($trips as $trip)
                    <tr>
                        <td>{{ $trip->trip_no }}</td>
                        <td>{{ $trip->vehicle->vehicle_no ?? '-' }}</td>
                        <td>{{ $trip->driver->name ?? '-' }}</td>
                        <td>{{ $trip->tripDetails->count() }}</td>
                        <td>{{ $trip->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.viewTripVehicleReport', $trip->id) }}" class="btn btn-sm btn-info">View Report</a>                           
                        </td>
                    </tr>
                @endforeach --}}
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')

<script>
    $(document).ready(function() {
        var DataTable = $("#ordersTable").DataTable({
            buttons: [{
                extend: "csv",
                className: "btn-sm"
            }],
            responsive: true,
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: `{{route('admin.tripVehicleReport')}}`,
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
                    data: 'driver',
                    name: 'driver'
                },
                {
                    data: 'total_journeys',
                    name: 'total_journeys'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
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
      
    });
</script>
@endsection

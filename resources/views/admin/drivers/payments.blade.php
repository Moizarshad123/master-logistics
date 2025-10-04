@extends('admin.layouts.app')
@section('title', 'Driver Payments')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
    crossorigin="anonymous">
@endsection

@section('content')
<!-- content -->
<div class="content ">

    <div class="mb-4">
        <h3>Driver Payments</h3>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="ordersTable">
            <thead>
                <tr>
                    <th>Trip ID</th>
                    <th>Payment Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $driver)
                <tr>
                   
                    <td>{{ $driver->trip_id }}</td>
                    <td>{{ $driver->payment_type }}</td>
                    <td>{{ $driver->amount }}</td>
                    <td>{{ date('d-m-Y', strtotime($driver->date)) }}</td>
                    <td>{{ $driver->comments }}</td>
                </tr>
                @empty
                <tr>
                    <th colspan="9">
                        <p class="text-center">No Payments Found</p>
                    </th>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="float:right">
            {{ $payments->links() }}
        </div>
    </div>


</div>
<!-- ./ content -->
@endsection

@section('js')
<script>
  

</script>
@endsection

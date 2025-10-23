@extends('admin.layouts.app')
@section('title', 'Sales Sheet')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
@endsection

@section('content')
  <!-- content -->
  <div class="content ">

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Sell Sheet</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.sales.create') }}" class="btn btn-sm btn-success">Add Sales</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="ordersTable">
            <thead>
                <tr>
                    <th>Station</th>
                    <th>Minimum Rent</th>
                    <th>Per Bag Rate</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $item)
                    <tr>
                        <td>{{ $item->station }}</td>
                        <td>{{ $item->minimum_rent }}</td>
                        <td>{{ $item->per_bag_rate }}</td>
                        <td>
                            <a href="{{ route('admin.sales.edit', $item)}}" class="btn btn-success btn-sm">Edit</a>
                            <form action="{{ route('admin.sales.destroy', $item) }}"  method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger deleteExpenseType" >Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                <tr>
                    <th colspan="2">
                        <p class="text-center">No Sells Found</p>
                    </th>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="float:right">
            {{ $sales->links() }}
        </div>
    </div>


</div>
<!-- ./ content -->
@endsection
@section('js')
<script>
    $(document).ready(function () {
        $('.deleteExpenseType').on('click', function (e) {

            e.preventDefault();
            const form = $(this).closest('form');

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to delete this expnese?",
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
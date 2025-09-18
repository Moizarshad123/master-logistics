@extends('admin.layouts.app')
@section('title', 'Expense Types')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
@endsection

@section('content')
  <!-- content -->
  <div class="content ">

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Expense Types</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.expense-types.create') }}" class="btn btn-sm btn-success">Add Expense Types</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="ordersTable">
            <thead>
                <tr>
                    <th>Expense Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expenseTypes as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>
                            <a href="{{ route('admin.expense-types.edit', $item->id)}}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('admin.expense-types.destroy', $item) }}"  method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger deleteExpenseType" >Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                <tr>
                    <th colspan="2">
                        <p class="text-center">No Expense Type Found</p>
                    </th>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="float:right">
            {{ $expenseTypes->links() }}
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
@extends('admin.layouts.app')
@section('title', 'Purchase Sheet')

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
@endsection

@section('content')
  <!-- content -->
  <div class="content ">

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10">
                <h3>Expense From</h3>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.expense-from.create') }}" class="btn btn-primary mb-3">+ Add New</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-custom table-lg mb-0" id="ordersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($expenseForms as $item)
                   <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->name }}</td>
                    <td>
                        <a href="{{ route('admin.expense-from.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.expense-from.destroy', $item->id) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete this?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <th colspan="2">
                        <p class="text-center">No Expense From Found</p>
                    </th>
                </tr>
                @endforelse
            </tbody>
        </table>
        
    </div>


</div>
<!-- ./ content -->
@endsection
@section('js')
<script>
   

</script>
@endsection
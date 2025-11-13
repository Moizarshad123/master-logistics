@extends('admin.layouts.app')
@section('title', 'Add Customer')

@section('css')
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Add Customer</h3>
    <form action="{{ route('admin.customers.store') }}" method="POST" enctype="multipart/form-data" id="expenseTypeForm">
        @csrf
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th style="width: 80%">Customer Name</th>
                    <th style="width: 20%">Action</th>
                </tr>
            </thead>
            <tbody id="tripDetailsContainer">
                <!-- Dynamic rows will appear here -->
            </tbody>
        </table>

        <button style="border-radius: 25%;float: right;margin-top: 20px;" id="addRow" class="btn btn-primary mb-3">
            <i class="fas fa-plus"></i>
        </button>

        <button type="submit"  style="margin-top: 25px;" class="btn btn-success" id="addExpenseType">Add Customers</button>
    </form>

</div>
<!-- / Content -->
@endsection

@section('js')
 <script>
        $(document).ready(function () {
            // Add new row
            $("#addRow").click(function (e) {
                e.preventDefault();
                let row = `
                    <tr>
                        <td>
                            <input type="text" name="name[]" class="form-control" placeholder="Enter Customer Name" required>
                        </td>
                        <td class="text-center">
                            <button type="button" style="border-radius: 25%;" class="btn btn-danger btn-sm removeRow">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>`;

                $("#tripDetailsContainer").append(row);
            });

            // Remove row
            $(document).on("click", ".removeRow", function () {
                $(this).closest("tr").remove();
            });
        });
    </script>
@endsection

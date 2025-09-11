@extends('admin.layouts.app')

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" crossorigin="anonymous">
@endsection

@section('content')
    <div class="container">
        <h4>Vehicle: {{ $vehicle->vehicle_no }}</h4>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Expenses:</h5>
            <!-- Add Button -->
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="fa fa-plus"></i> Add Expense
            </button>
        </div>

        @if($vehicle->expenseTypes->isEmpty())
            <p>No expenses added for this vehicle.</p>
        @else
            <ul class="list-group">
                @foreach($vehicle->expenseTypes as $expense)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $expense->name }}
                        <div>
                            <!-- Edit Button -->
                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editExpenseModal{{ $expense->id }}">
                                Edit
                            </button>
                            <!-- Delete Button -->
                            <form action="{{ route('admin.vehicles.expenses.delete', [$vehicle->id, $expense->id]) }}" 
                                method="POST" style="display:inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this expense?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </li>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editExpenseModal{{ $expense->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('admin.vehicles.expenses.update', [$vehicle->id, $expense->id]) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Expense</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <select name="expense_type_id" class="form-select">
                                            @foreach($allExpenses as $exp)
                                                <option value="{{ $exp->id }}" 
                                                    {{ $expense->id == $exp->id ? 'selected' : '' }}>
                                                    {{ $exp->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </ul>
        @endif
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.vehicles.expenses.store', $vehicle->id) }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <select name="expense_type_id" class="form-select">
                            @foreach($allExpenses as $exp)
                                <option value="{{ $exp->id }}">{{ $exp->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Add</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endsection

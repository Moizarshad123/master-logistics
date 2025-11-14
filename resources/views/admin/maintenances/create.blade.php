@extends('admin.layouts.app')
@section('title', 'Add Maintenance')

@section('content')
<div class="container">
    <h2>Add Maintenance</h2>

    <form action="{{ route('admin.maintenances.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label>Vehicle:</label>
                    <select name="vehicle_id" class="form-select" required>
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}">{{ $v->vehicle_no }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-4">
                <div class="mb-3">
                    <label>Expense Type:</label>
                    <select name="expense_id" class="form-select" required>
                        <option value="">Select Expense</option>
                        @foreach($expenses as $expense)
                            <option value="{{ $expense->id }}">{{ $expense->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>                  
            <div class="col-md-4">
                <div class="mb-3">
                    <label>Amount:</label>
                    <input type="number" name="amount" class="form-control" step="0.01" required>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label>Comments:</label>
            <textarea name="comments" class="form-control" rows="3"></textarea>
        </div>
        <button class="btn btn-success">Save</button>
    </form>
</div>
@endsection

@extends('admin.layouts.app')
@section('title', 'Edit Maintenance')

@section('content')
<div class="container">
    <h2>Edit Maintenance</h2>

    <form action="{{ route('admin.maintenances.update', $maintenance) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4">
                <div class="mb-3">
                    <label>Vehicle:</label>
                    <select name="vehicle_id" class="form-select" required>
                        <option value="">Select Vehicle</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ $maintenance->vehicle_id == $v->id ? 'selected' : '' }}>
                                {{ $v->vehicle_no }}
                            </option>
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
                            <option value="{{ $expense->id }}" {{ $maintenance->expense_id == $expense->id ? 'selected' : '' }}>{{ $expense->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>         
            <div class="col-md-4">
                <div class="mb-3">
                    <label>Amount:</label>
                    <input type="number" name="amount" value="{{ $maintenance->amount }}" class="form-control" step="0.01" required>
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label>Comments:</label>
                    <textarea name="comments" class="form-control" rows="3">{{ $maintenance->comments }}</textarea>
                </div>
            </div>
        </div>


        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection

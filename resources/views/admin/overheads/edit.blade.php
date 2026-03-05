{{-- resources/views/admin/overheads/edit.blade.php --}}

@extends('admin.layouts.app')

@section('title', 'Edit Overhead')

@section('content')

    <div class="mb-4">
        <div class="row">
            <div class="col-md-10"><h3>Edit Overhead</h3></div>
            <div class="col-md-2">
                <a href="{{ route('admin.overheads.index') }}" class="btn btn-sm btn-secondary">Back</a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <form action="{{ route('admin.overheads.update', $overhead->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">

                    {{-- Expense Type --}}
                    <div class="col-md-6 mb-3">
                        <label>Expense Type <span class="text-danger">*</span></label>
                        <select name="expense_type_id" id="expenseTypeSelect"
                                class="form-control @error('expense_type_id') is-invalid @enderror" required>
                            <option value="">-- Select Expense Type --</option>
                            @foreach($expenseTypes as $type)
                                <option value="{{ $type->id }}"
                                        data-name="{{ strtolower($type->name) }}"
                                    {{ old('expense_type_id', $overhead->expense_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('expense_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Driver — only for welfare --}}
                    <div class="col-md-6 mb-3" id="driverWrapper" style="display:none;">
                        <label>Driver <span class="text-muted">(Late Status)</span></label>
                        <select name="driver_id" id="driverSelect"
                                class="form-control @error('driver_id') is-invalid @enderror">
                            <option value="">-- Select Driver --</option>
                            @foreach($lateDrivers as $driver)
                                <option value="{{ $driver->id }}"
                                    {{ old('driver_id', $overhead->driver_id) == $driver->id ? 'selected' : '' }}>
                                    {{ $driver->name }} — {{ $driver->emp_id }}
                                </option>
                            @endforeach
                        </select>
                        @error('driver_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($lateDrivers->isEmpty())
                            <small class="text-warning">No drivers with late status found.</small>
                        @endif
                    </div>

                    {{-- Amount --}}
                    <div class="col-md-6 mb-3">
                        <label>Amount (Rs.) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="amount"
                               class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount', $overhead->amount) }}" required>
                        @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Date --}}
                    <div class="col-md-6 mb-3">
                        <label>Date <span class="text-danger">*</span></label>
                        <input type="date" name="date"
                               class="form-control @error('date') is-invalid @enderror"
                               value="{{ old('date', $overhead->date->format('Y-m-d')) }}" required>
                        @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    {{-- Comment --}}
                    <div class="col-md-12 mb-3">
                        <label>Comment</label>
                        <textarea name="comment" rows="3"
                                  class="form-control @error('comment') is-invalid @enderror"
                                  placeholder="Optional note...">{{ old('comment', $overhead->comment) }}</textarea>
                        @error('comment') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                </div>

                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-sync-alt"></i> Update Overhead
                </button>
                <a href="{{ route('admin.overheads.index') }}" class="btn btn-secondary ml-2">Cancel</a>
            </form>
        </div>
    </div>

@endsection

@section('js')
<script>
    $(document).ready(function () {

        function toggleDriverDropdown() {
            var selectedName = $('#expenseTypeSelect').find('option:selected').data('name') || '';

            if (selectedName === 'welfare') {
                $('#driverWrapper').slideDown(200);
            } else {
                $('#driverWrapper').slideUp(200);
                $('#driverSelect').val('');
            }
        }

        $('#expenseTypeSelect').on('change', function () {
            toggleDriverDropdown();
        });

        // On load — edit mein agar welfare selected hai to show karo
        toggleDriverDropdown();

    });
</script>
@endsection
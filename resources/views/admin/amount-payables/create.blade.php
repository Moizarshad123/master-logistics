@extends('admin.layouts.app')
@section('title','Account Payable')

@section('content')

<form method="POST" enctype="multipart/form-data"
      action="{{ isset($amountPayable) ? route('admin.amount-payables.update',$amountPayable->id) : route('admin.amount-payables.store') }}">
    @csrf
    @if(isset($amountPayable)) @method('PUT') @endif

    <div class="row">
        <div class="col-md-4">
            <label>Supplier *</label>
            <select name="supplier_id" class="form-select" required>
                <option value="">Select Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}"
                        {{ isset($amountPayable) && $amountPayable->supplier_id == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label>Amount *</label>
            <input type="number" step="0.01" name="amount"
                   class="form-control"
                   value="{{ $amountPayable->amount ?? '' }}">
        </div>

        <div class="col-md-4">
            <label>Date</label>
            <input type="date" name="date"
                   class="form-control"
                   value="{{ $amountPayable->date ?? date('Y-m-d') }}">
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-7">
            <label>Payment Via *</label>
            <select name="payment_via" id="payment_via" class="form-select">
                <option value="">Select</option>
                @foreach(['Cheque','Net Cash','Online','Other Source'] as $type)
                    <option value="{{ $type }}"
                        {{ (isset($amountPayable) && $amountPayable->payment_via == $type) ? 'selected' : '' }}>
                        {{ $type }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-5" id="other_source_div" style="display:none;">
            <label>Other Source</label>
            <input type="text" name="other_source"
                   class="form-control"
                   value="{{ $amountPayable->other_source ?? '' }}">
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-6">
            <label>Receipt</label>
            <input type="file" name="receipt" class="form-control">
        </div>
    </div>

    <button class="btn btn-success mt-3">Save</button>
    <a href="{{ route('admin.amount-payables.index') }}" class="btn btn-secondary mt-3">Back</a>
</form>

@endsection

@section('js')
<script>
    function toggleOtherSource() {
        if ($('#payment_via').val() === 'Other Source') {
            $('#other_source_div').show();
        } else {
            $('#other_source_div').hide();
            $('#other_source_div input').val('');
        }
    }

    $('#payment_via').on('change', toggleOtherSource);
    $(document).ready(toggleOtherSource);
</script>
@endsection

@extends('admin.layouts.app')
@section('title', 'Add Account Receivables')

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
 <style>
    .row {
        margin-bottom: 15px;
    }
</style>    
@endsection

@section('content')
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
    <h3>Add Account Receivables</h3>
    <form action="{{ route('admin.amount-receivables.store') }}" method="POST" enctype="multipart/form-data" id="expenseTypeForm">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <label for="">Trip ID#</label>
                <select name="trip_id" id="trip_id" class="form-select select2">
                    <option value="">Select Trip</option>
                    @foreach($trips as $trip)
                        <option value="{{ $trip->id }}">{{ $trip->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="">Customer<span style="color: red">*</span></label>
                <select name="customer_id" id="customer_id" class="form-select select2">
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="">Payment  Via<span style="color: red">*</span></label>
                <select name="payment_type" id="payment_type" class="form-select" required>
                    <option value="">Select</option>
                    <option value="cheque">Cheque</option>
                    <option value="net cash">Net Cash</option>
                    <option value="online">Online</option>
                    <option value="other source">Other Source</option>
                </select>
            </div>
            <div class="col-md-6" id="other_source_div" style="display:none;">
                    <Label>Other Source Name</Label>
                    <input type="text" name="other_source" class="form-control mt-2" placeholder="Enter source name">
            </div>
            
        </div>
        <div class="row">
           <div class="col-md-4">
               <label for="">Receivable Amount<span style="color: red">*</span></label>
               <input type="number" min="1" name="amount" class="form-control" required>
           </div>
           <div class="col-md-4">
               <label for="">Amount Receivable Date<span style="color: red">*</span></label>
               <input type="date" name="date" class="form-control" required>
           </div>
           <div class="col-md-4">
                <label for="">Upload Payment Receipt</label>
                <input type="file" name="receipt" class="form-control">
            </div>
       </div>

       <button class="btn btn-sm btn-success" type="submit">Submit</button>
    </form>
</div>
<!-- / Content -->
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: true,
                width: '100%'
            });
        });
    </script>

 <script>
    document.getElementById('payment_type').addEventListener('change', function () {
        document.getElementById('other_source_div').style.display =
            this.value === 'other source' ? 'block' : 'none';
    });
</script>
@endsection

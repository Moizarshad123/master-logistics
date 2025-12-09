<div class="modal fade" id="endTripModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="width: 150%;">

            <div class="modal-header">
                <h5 class="modal-title">End Trip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="endTripForm" method="POST" action="{{ route('admin.endActualTrailerTrip')}}">
                @csrf
                <input type="hidden" name="trip_id_input" id="trip_id_input">
                <div class="modal-body">
                    <div class="row" style="margin-top:15px">
                        <div class="col-md-12">
                            <label for="">End Trip Date</label>
                            <input type="date" class="form-control" name="end_date" value="{{ date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="row" style="margin-top:15px">
                        <div class="col-md-4">
                            <label for="">Advance Amount</label>
                            <input type="number" class="form-control" readonly name="balance" id="balance">
                        </div>
                        <div class="col-md-4">
                            <label for="">Total Expense</label>
                            <input type="number" class="form-control" name="total_expense" id="total_expense">
                        </div>
                        <div class="col-md-4">
                            <label for="">Remaining Amount</label>
                            <input type="number" class="form-control" name="remaining_amount" id="remaining_amount">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Yes, End Trip</button>
                </div>
            </form>

        </div>
    </div>
</div>

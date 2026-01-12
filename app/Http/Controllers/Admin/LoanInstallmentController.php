<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoanInstallmentController extends Controller
{
    public function markPaid($id)
    {
        $installment = LoanInstallment::findOrFail($id);
        $installment->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);

        return response()->json(['message' => 'Installment marked paid']);
    }
}

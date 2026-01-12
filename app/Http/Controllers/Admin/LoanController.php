<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Driver;
use App\Models\LoanInstallment;
use DB;


class LoanController extends Controller
{
    public function index()
    {
        $loans = Loan::with("driver")->orderByDESC("id")->get();
        return view("admin.loan.index", compact("loans"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $drivers = Driver::orderBy("name", "ASC")->get();
        return view("admin.loan.create", compact("drivers"));
        
    }

    public function store(Request $request)
    {
        try {

            $check_loan = Loan::where("driver_id", $request->driver_id)->where("status", "unpaid")->first();

            if($check_loan == null) {
                $request->validate([
                    'driver_id'   => 'required',
                    'tenure_from' => 'required|date',
                    'tenure_to'   => 'required|date',
                    'amount'      => 'required|numeric'
                ]);
                DB::beginTransaction();

                $start = Carbon::parse($request->tenure_from . '-01');
                $end   = Carbon::parse($request->tenure_to . '-01');
        
                $months             = Carbon::parse($request->tenure_from)->diffInMonths(Carbon::parse($request->tenure_to)) + 1;
                // $monthlyInstallment = $request->amount / $months;
                $loan = Loan::create([
                                        'driver_id'   => $request->driver_id,
                                        'tenure_from' => $start,
                                        'tenure_to'   => $end,
                                        'amount'      => $request->amount,
                                        'total_months'=> $request->total_months,
                                        'monthly_installment' => $request->monthly_installment
                                    ]);
        
                // Auto-generate installments
                $date = Carbon::parse($request->tenure_from);
                for ($i = 0; $i < $months; $i++) {
                    LoanInstallment::create([
                                                'loan_id' => $loan->id,
                                                'month'   => $date->month,
                                                'year'    => $date->year,
                                                'amount'  => $request->monthly_installment
                                            ]);
                    $date->addMonth();
                }
                DB::commit();
                return redirect('admin/loans')->with("success", 'Loan created successfully');
            } else {
                return redirect()->back()->with("error", 'This driver already have ongoing loan');
            }
        } catch (\Exception $e) {
            DB::rollback();
           return redirect()->back()->with("error", $e->getMessage());
        }
    }

    public function show($id)
    {
        $installments = LoanInstallment::where('loan_id' ,$id)->get();
        return view("admin.loan.show", compact("installments"));

    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy(Loan $loan)
    {
        LoanInstallment::where("loan_id", $loan->id)->delete();
        $loan->delete();
        return redirect()->route('admin.loans.index')->with('success', 'Loan deleted successfully.');
    }
    
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\AdvanceSalary;
use App\Models\LoanInstallment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year  = $request->year  ?? now()->year;

        $drivers = Driver::with(['attendances' => function ($q) use ($month, $year) {
                                $q->whereMonth('date', $month)
                                ->whereYear('date', $year);
                            }])->orderBy("name", 'ASC')->get();

        $report = [];

        foreach ($drivers as $driver) {
            $present = $driver->attendances->where('status', 'present')->count();
            $absent  = $driver->attendances->where('status', 'absent')->count();
            $leave   = $driver->attendances->where('status', 'leave')->count();

            $totalDays = $present + $absent + $leave;

            $advance = AdvanceSalary::where('driver_id', $driver->id)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->sum('amount');

            $loanDeduction = LoanInstallment::whereHas('loan', function ($q) use ($driver) {
                                        $q->where('driver_id', $driver->id);
                                    })
                                    ->where('month', $month)
                                    ->where('year', $year)
                                    ->where('status', 'unpaid')
                                    ->sum('amount'); 

            $perDaySalary = $driver->salary;
            $grossSalary  = $totalDays * $perDaySalary;   // Gross salary for all recorded days
            $deduction    = $absent * $perDaySalary;      // Deduct for absent days only
            $netSalary    = $grossSalary - $deduction - $advance - round($loanDeduction); 

            $report[] = [
                'driver'       => $driver,
                'totalDays'    => $totalDays,
                'present'      => $present,
                'absent'       => $absent,
                'leave'        => $leave,
                'grossSalary'  => $grossSalary,
                'deduction'    => $deduction,
                'netSalary'    => $netSalary,
                "advance"      => $advance,
                "loanDeduction" => round($loanDeduction),
            ];
        }

        return view('admin.payroll.index', compact(
            'report','month','year'
        ));
    }

    public function download(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;

        $drivers = Driver::with(['attendances' => function ($q) use ($month, $year) {
                            $q->whereMonth('date', $month)
                            ->whereYear('date', $year);
                        }])->orderBy('name')->get();

        $report = [];

        foreach ($drivers as $driver) {
            $present = $driver->attendances->where('status', 'present')->count();
            $absent  = $driver->attendances->where('status', 'absent')->count();
            $leave   = $driver->attendances->where('status', 'leave')->count();

            // Total recorded days (Present + Absent + Leave)
            $totalDays = $present + $absent + $leave;

            $perDaySalary = $driver->salary;


            $advance = AdvanceSalary::where('driver_id', $driver->id)
                                ->where('month', $month)
                                ->where('year', $year)
                                ->sum('amount');

            $loanDeduction = LoanInstallment::whereHas('loan', function ($q) use ($driver) {
                                        $q->where('driver_id', $driver->id);
                                    })
                                    ->where('month', $month)
                                    ->where('year', $year)
                                    ->where('status', 'unpaid')
                                    ->sum('amount');


            // Correct calculation
            $grossSalary  = $totalDays * $perDaySalary;   // Gross salary for all recorded days
            $deduction    = $absent * $perDaySalary;      // Deduct for absent days only
            $netSalary    = $grossSalary - $deduction - $advance;    // Net salary

            $report[] = [
                'driver'       => $driver,
                'totalDays'    => $totalDays,
                'present'      => $present,
                'absent'       => $absent,
                'leave'        => $leave,
                'grossSalary'  => $grossSalary,
                'deduction'    => $deduction,
                'netSalary'    => $netSalary,
            ];
        }

        $pdf = Pdf::loadView('admin.payroll.pdf', compact(
            'report','month','year'
        ));

        return $pdf->download("Payroll-{$month}-{$year}.pdf");
    }

    public function deductLoan()
    {
        try {

            DB::beginTransaction();

            $month = Carbon::now()->month;
            $year  = Carbon::now()->year;

            // Get all unpaid installments of current month/year
            $installments = LoanInstallment::where('month', $month)
                                            ->where('year', $year)
                                            ->where('status', 'unpaid')
                                            ->whereHas('loan')
                                            ->get();

            if ($installments->isEmpty()) {
                return redirect()->back()->with('error', 'No unpaid loan installments found for current month');
            }

            // Mark all as PAID
            foreach ($installments as $installment) {
                $installment->update([
                    'status'  => 'paid',
                    'paid_at' => now(),
                ]);
            }
            DB::commit();
            return redirect()->back()->with('success','Loan installments deducted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}


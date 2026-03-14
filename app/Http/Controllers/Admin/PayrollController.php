<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\AdvanceSalary;
use App\Models\Attendance;


use App\Models\LoanInstallment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = (int) ($request->month ?? now()->month);
        $year  = (int) ($request->year  ?? now()->year);

        // Format: "2026-03" — new attendance table ka month column
        $monthStr = sprintf('%04d-%02d', $year, $month);

        // $totalDaysInMonth = Carbon::createFromDate($year, $month, 1)->daysInMonth;

        $totalDaysInMonth = 30.4;
        $drivers          = Driver::where('status', 'active')->orderBy('name', 'ASC')->get();

        // Sab attendances ek query mein — keyed by driver_id
        $attendances = Attendance::where('month', $monthStr)
                                ->get()
                                ->keyBy('driver_id');

        $report = [];

        foreach ($drivers as $driver) {
            $att = $attendances[$driver->id] ?? null;

            $present = $att->present_days ?? 0;
            $absent  = $att->absent_days  ?? 0;
            $leave   = $att->leave_days   ?? 0;

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

            $monthlySalary = $driver->salary;
            $perDaySalary = round($driver->salary / $totalDaysInMonth, 2);
            $grossSalary = $present * $perDaySalary;
            $deduction   = $absent  * $perDaySalary;
            $netSalary   = $grossSalary - $deduction - $advance - round($loanDeduction);

            // $perDaySalary  = $totalDaysInMonth > 0 ? round($monthlySalary / $totalDaysInMonth, 2) : 0;
            // $grossSalary = $present * $perDaySalary;
            // $deduction   = $absent  * $perDaySalary;
            // $netSalary   = $grossSalary - $advance - round($loanDeduction);

            $report[] = [
                'driver'        => $driver,
                'totalDays'     => $totalDaysInMonth,
                'present'       => $present,
                'absent'        => $absent,
                'leave'         => $leave,
                'monthlySalary' => $monthlySalary,
                'perDaySalary'  => round($perDaySalary),
                'grossSalary'   => round($grossSalary),
                'deduction'     => round($deduction),
                'netSalary'     => round($netSalary),
                'advance'       => $advance,
                'loanDeduction' => round($loanDeduction),
            ];
        }

        return view('admin.payroll.index', compact(
            'report', 'month', 'year', 'totalDaysInMonth'
        ));
    }
    public function download(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;
        $monthStr = sprintf('%04d-%02d', $year, $month);

        $drivers = Driver::where('status', 'active')->orderBy('name', 'ASC')->get();

        // Sab attendances ek query mein — keyed by driver_id
        $attendances = Attendance::where('month', $monthStr)
                                    ->get()
                                    ->keyBy('driver_id');

        $report           = [];
        $totalDaysInMonth = 30.4;

        foreach ($drivers as $driver) {
            $att = $attendances[$driver->id] ?? null;

            $present = $att->present_days ?? 0;
            $absent  = $att->absent_days  ?? 0;
            $leave   = $att->leave_days   ?? 0;

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


            $monthlySalary = $driver->salary;
            $perDaySalary = round($monthlySalary / $totalDaysInMonth, 2);
            $grossSalary  = $present * $perDaySalary;
            $deduction   = $absent  * $perDaySalary;
            $netSalary   = $grossSalary - $deduction - $advance - round($loanDeduction);

            $report[] = [
                'driver'         => $driver,
                'totalDays'      => $totalDaysInMonth,
                'present'        => $present,
                'absent'         => $absent,
                'leave'          => $leave,
                'monthlySalary'  => $monthlySalary,
                'perDaySalary'   => round($perDaySalary),
                'grossSalary'    => $grossSalary,
                'deduction'      => $deduction,
                'netSalary'      => $netSalary,
                 'advance'       => $advance,
                'loanDeduction'  => round($loanDeduction),
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


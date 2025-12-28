<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    AdminController,
    ExpenseTypeController,
    DriverController,
    VehicleController,
    WheelerController,
    TripController,
    ReportController,
    DestinationController,
    PurchaseSheetController,
    SalesSheetController,
    ExpenseFromController,
    MaterialController,
    ExpenseCategoryController,
    CustomerController,
    CustomerHeadController,
    MaintenanceController,
    DieselController,
    AmountReceivableController
};

Route::match(['get', 'post'], 'login', [AdminController::class, 'login'])->name('login');
Route::match(['get', 'post'], 'register', [AdminController::class, 'register'])->name('register');
Route::get('logout', function (){
            auth()->logout();
            return redirect('/');
        })->name('admin.logout');
Route::get('/', function (){
    return redirect('login');
});

Route::prefix('admin')->middleware('admin')->name('admin.')->group(function () {
    Route::controller(AdminController::class)->group(function() {
        Route::get('dashboard', 'dashboard')->name('dashboard');
    });

    Route::resource('amount-receivables', AmountReceivableController::class);
    Route::resource('diesel', DieselController::class);
    Route::resource('expense-from', ExpenseFromController::class);
    Route::resource('expense-types', ExpenseTypeController::class);
    Route::resource('drivers', DriverController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('wheelers', WheelerController::class);
    Route::resource('trips', TripController::class);
    Route::resource('destinations', DestinationController::class);
    Route::resource('purchases', PurchaseSheetController::class);
    Route::resource('sales', SalesSheetController::class);
    Route::resource('materials', MaterialController::class);
    Route::resource('expense-categories', ExpenseCategoryController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('customer-heads', CustomerHeadController::class);
    Route::resource('maintenances', MaintenanceController::class);
    Route::get('salesheets/{id}', [SalesSheetController::class, 'show_sheet']);
    Route::get('purchasesheets/{id}', [PurchaseSheetController::class, 'show_sheet']);
    Route::get('driver-payments/{id}', [DriverController::class, "driver_payments"])->name("driverPayments");
    Route::get('get-vehicle-expenses', [VehicleController::class, 'getVehicleExpenses'])->name('getVehicleExpenses');
        
    Route::controller(TripController::class)->group(function() {
        Route::POST('end-actual-trip', 'endActualTrip')->name('endActualTrip');
        Route::POST('end-actual-trailer-trip', 'endActualTrailerTrip')->name('endActualTrailerTrip');
        Route::get('closed-trips', 'closedTrips')->name('closedTrips');
        Route::get('active-trailers-trips', 'activeTrailersTrips')->name('activeTrailersTrips');
        Route::get('closed-trailers-trips', 'closedTrailersTrips')->name('closeTrailersTrips');
        Route::get('disbursement-slip', 'disbursement_slip')->name('disbursementSlip');
    });

    Route::controller(CustomerHeadController::class)->group(function() {
        Route::get('customer-head-report', 'customerHeadReport')->name('customerHeadReport');
    });



    Route::controller(ReportController::class)->group(function() {
        Route::get('vehicle-summary-report', 'vehicleSummaryReport')->name("vehicleSummaryReport");
        Route::get('trip-vehicle-report', 'tripVehicleReport')->name("tripVehicleReport");
        Route::get('profit-and-loss-report', 'profit_and_loss')->name("profitAndLossReport");
        Route::get('weekly-labour-report', 'weekly_labour_report')->name("weeklyLabourReport");
        Route::get('view-weekly-labour-report', 'view_weekly_labour_report')->name("viewWeeklyLabourReport");
        Route::get('baloch-labour-report', 'baloch_labour_report')->name("balochLabourReport");
        Route::get('view-baloch-labour-report', 'view_baloch_labour_report')->name("viewBalochLabourReport");
        Route::get('view-trip-vehicle-report/{id}', 'viewTripVehicleReport')->name("viewTripVehicleReport");
    });

    // Route::get('/endtrip/{vehicle}/expenses', [VehicleController::class, 'expenses'])->name('vehicles.expenses');
    Route::POST('endtrip', [TripController::class, 'endTrip']);
    Route::controller(VehicleController::class)->group(function() {
        Route::get('/vehicles/{vehicle}/expenses', 'expenses')->name('vehicles.expenses');
        Route::post('{vehicle}/expenses', 'storeExpense')->name('vehicles.expenses.store');
        Route::put('{vehicle}/expenses/{expense}', 'updateExpense')->name('vehicles.expenses.update');
        Route::delete('{vehicle}/expenses/{expense}', 'deleteExpense')->name('vehicles.expenses.delete');
    });
});

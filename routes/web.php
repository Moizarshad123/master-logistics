<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ExpenseTypeController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\WheelerController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\DestinationController;
use App\Http\Controllers\Admin\PurchaseSheetController;
use App\Http\Controllers\Admin\SalesSheetController;
use App\Http\Controllers\Admin\ExpenseFromController;
use App\Http\Controllers\Admin\MaterialController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\CustomerController;





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

    
    Route::get('salesheets/{id}', [SalesSheetController::class, 'show_sheet']);
    Route::get('purchasesheets/{id}', [PurchaseSheetController::class, 'show_sheet']);

    Route::get('driver-payments/{id}', [DriverController::class, "driver_payments"])->name("driverPayments");
    Route::get('get-vehicle-expenses', [VehicleController::class, 'getVehicleExpenses'])->name('getVehicleExpenses');

    // Route::get('/endtrip/{vehicle}/expenses', [VehicleController::class, 'expenses'])->name('vehicles.expenses');
    Route::POST('endtrip', [TripController::class, 'endTrip']);
    Route::get('trip-vehicle-report', [ReportController::class, 'tripVehicleReport'])->name("tripVehicleReport");
    Route::get('profit-and-loss-report', [ReportController::class, 'profit_and_loss'])->name("profitAndLossReport");

    
    Route::get('view-trip-vehicle-report/{id}', [ReportController::class, 'viewTripVehicleReport'])->name("viewTripVehicleReport");


    Route::get('/vehicles/{vehicle}/expenses', [VehicleController::class, 'expenses'])->name('vehicles.expenses');
    Route::post('{vehicle}/expenses', [VehicleController::class, 'storeExpense'])->name('vehicles.expenses.store');
    Route::put('{vehicle}/expenses/{expense}', [VehicleController::class, 'updateExpense'])->name('vehicles.expenses.update');
    Route::delete('{vehicle}/expenses/{expense}', [VehicleController::class, 'deleteExpense'])->name('vehicles.expenses.delete');
});

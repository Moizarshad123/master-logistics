<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ExpenseTypeController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Controllers\Admin\WheelerController;
use App\Http\Controllers\Admin\TripController;


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
    Route::resource('expense-types', ExpenseTypeController::class);
    Route::resource('drivers', DriverController::class);
    Route::resource('vehicles', VehicleController::class);
    Route::resource('wheelers', WheelerController::class);
    Route::resource('trips', TripController::class);

    Route::get('get-vehicle-expenses', [VehicleController::class, 'getVehicleExpenses'])->name('getVehicleExpenses');

    // Route::get('/endtrip/{vehicle}/expenses', [VehicleController::class, 'expenses'])->name('vehicles.expenses');
    Route::get('endtrip/{id}', [TripController::class, 'endTrip']);


    Route::get('/vehicles/{vehicle}/expenses', [VehicleController::class, 'expenses'])->name('vehicles.expenses');
    Route::post('{vehicle}/expenses', [VehicleController::class, 'storeExpense'])->name('vehicles.expenses.store');
    Route::put('{vehicle}/expenses/{expense}', [VehicleController::class, 'updateExpense'])->name('vehicles.expenses.update');
    Route::delete('{vehicle}/expenses/{expense}', [VehicleController::class, 'deleteExpense'])->name('vehicles.expenses.delete');
});

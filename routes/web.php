<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessSettingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\MaterialCategoryController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductionTaskController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.store');
    Route::get('register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::patch('employees/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('employees.toggle-status');
        Route::resource('employees', EmployeeController::class);
        Route::get('settings', [BusinessSettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [BusinessSettingController::class, 'update'])->name('settings.update');
    });

    Route::middleware('role:admin,sales')->group(function () {
        Route::patch('clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])->name('clients.toggle-status');
        Route::resource('clients', ClientController::class);
        Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
        Route::resource('products', ProductController::class);
        Route::patch('orders/{order}/toggle-status', [OrderController::class, 'toggleStatus'])->name('orders.toggle-status');
        Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::patch('orders/{order}/advance', [OrderController::class, 'advance'])->name('orders.advance');
        Route::resource('orders', OrderController::class);
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    });

    Route::middleware('role:admin,production')->group(function () {
        Route::patch('material-categories/{material_category}/toggle-status', [MaterialCategoryController::class, 'toggleStatus'])->name('material-categories.toggle-status');
        Route::resource('material-categories', MaterialCategoryController::class);
        Route::patch('materials/{material}/toggle-status', [MaterialController::class, 'toggleStatus'])->name('materials.toggle-status');
        Route::resource('materials', MaterialController::class);
        Route::patch('inventory-movements/{inventory_movement}/toggle-status', [InventoryMovementController::class, 'toggleStatus'])->name('inventory-movements.toggle-status');
        Route::patch('inventory-movements/{inventory_movement}/reverse', [InventoryMovementController::class, 'reverse'])->name('inventory-movements.reverse');
        Route::resource('inventory-movements', InventoryMovementController::class)->only(['index', 'create', 'store', 'show']);
        Route::patch('production-tasks/{production_task}/toggle-status', [ProductionTaskController::class, 'toggleStatus'])->name('production-tasks.toggle-status');
        Route::resource('production-tasks', ProductionTaskController::class);
    });
});

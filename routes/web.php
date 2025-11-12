<?php

use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Category
    Route::resource('categories', CategoryController::class);
    Route::post('/categories/update-order', [CategoryController::class, 'updateOrder'])->name('categories.updateOrder');

    // Transaction
    Route::resource('transactions', TransactionController::class);

    // Budeget
    Route::resource('budgets', BudgetController::class);
    Route::post('/budgets/copy', [BudgetController::class, 'copyStore'])->name('budgets.copy.store');

    // RecurringTransaction
    Route::resource('recurring-transactions', RecurringTransactionController::class);

    // Report
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Tag
    Route::resource('tags', TagController::class);
});

require __DIR__.'/auth.php';

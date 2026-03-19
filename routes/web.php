<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/company', [CompanyController::class, 'index'])->name('company.index');
        Route::get('/company/paginate', [CompanyController::class, 'paginate'])->name('company.paginate');
        Route::get('/company/create', [CompanyController::class, 'create'])->name('company.create');
        Route::post('/company/store', [CompanyController::class, 'store'])->name('company.store');
        Route::get('/company/{company_id}/edit', [CompanyController::class, 'edit'])->name('company.edit');
        Route::post('/company/{company_id}/update', [CompanyController::class, 'update'])->name('company.update');
        Route::delete('/company/{company_id}/delete', [CompanyController::class, 'destroy'])->name('company.delete');

        Route::get('/employee', [EmployeeController::class, 'index'])->name('employee.index');
        Route::get('/employee/paginate', [EmployeeController::class, 'paginate'])->name('employee.paginate');
        Route::get('/employee/store', [EmployeeController::class, 'store'])->name('employee.store');
        Route::get('/employee/{employee_id}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
        Route::delete('/employee/{employee_id}/delete', [EmployeeController::class, 'destroy'])->name('employee.delete');

    });
});

require __DIR__.'/settings.php';

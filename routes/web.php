<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PlaceholderController as AdminPlaceholderController;
use App\Http\Controllers\Admin\SalespersonApplicationController as AdminSalespersonApplicationController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\PlaceholderController as UserPlaceholderController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SalespersonApplicationController as UserSalespersonApplicationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/auth/google', [AuthController::class, 'googleStub'])->name('auth.google');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/clients', [ClientController::class, 'index'])->name('clients');

        Route::get('/salesperson-applications', [AdminSalespersonApplicationController::class, 'index'])->name('salesperson-applications');
        Route::post('/salesperson-applications/{user}/approve', [AdminSalespersonApplicationController::class, 'approve'])->name('salesperson-applications.approve');
        Route::post('/salesperson-applications/{user}/reject', [AdminSalespersonApplicationController::class, 'reject'])->name('salesperson-applications.reject');

        Route::get('/courses', [AdminPlaceholderController::class, 'courses'])->name('courses');
        Route::get('/certificates', [AdminPlaceholderController::class, 'certificates'])->name('certificates');
        Route::get('/sales-manuals', [AdminPlaceholderController::class, 'manuals'])->name('manuals');
        Route::get('/social-guide', [AdminPlaceholderController::class, 'socialGuide'])->name('social-guide');
        Route::get('/settings', [AdminPlaceholderController::class, 'settings'])->name('settings');
    });

Route::middleware(['auth', 'role:user'])
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/apply-salesperson', [UserSalespersonApplicationController::class, 'show'])->name('apply-salesperson');
        Route::post('/apply-salesperson', [UserSalespersonApplicationController::class, 'apply'])->name('apply-salesperson.apply');

        Route::get('/training', [UserPlaceholderController::class, 'training'])->name('training');
        Route::get('/certificates', [UserPlaceholderController::class, 'certificates'])->name('certificates');
        Route::get('/sales-manuals', [UserPlaceholderController::class, 'manuals'])->name('manuals');
        Route::get('/social-guide', [UserPlaceholderController::class, 'socialGuide'])->name('social-guide');
    });

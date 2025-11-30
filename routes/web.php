<?php

use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }
        $habits = auth()->user()->habits()->with(['logs' => function ($q) {
            $q->where('date', now()->format('Y-m-d'));
        }])->get();
        return view('dashboard', compact('habits'));
    })->name('dashboard');

    Route::get('/admin/dashboard', [HabitController::class, 'index'])->name('admin.dashboard');

    // Internal API Routes (Session Auth)
    Route::get('/api/habits', [\App\Http\Controllers\HabitApiController::class, 'index'])->name('api.habits.index');
    Route::post('/api/habits/{habit}/toggle', [\App\Http\Controllers\HabitApiController::class, 'toggle'])->name('api.habits.toggle');

    // User Management
    Route::resource('users', \App\Http\Controllers\UserController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::post('/habits', [HabitController::class, 'store'])->name('habits.store');
    Route::delete('/habits/{habit}', [HabitController::class, 'destroy'])->name('habits.destroy');

    Route::post('/habits/{habit}/toggle', [HabitLogController::class, 'toggle'])->name('habits.toggle');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

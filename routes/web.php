<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsersController;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register')
    ]);
});

Route::get('/dashboard', function () {
    $user = Auth::user();
    $roles = ['super_administrator', 'administrator', 'user'];
    $userRole = $user->roles()->whereIn('name', $roles)->first();
    return Inertia::render('Dashboard', [
        'role' => $userRole ? $userRole->name : null,
        'users' => User::where('id', '!=', 1)->select('id','name', 'email')->get()
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::resource('users', UsersController::class);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

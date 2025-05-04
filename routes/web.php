<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', [ProjectController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/projects/create', [ProjectController::class, 'create']);
Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
Route::post('/project/update-status', [ProjectController::class, 'updateStatus'])->name('projects.update.status');
Route::get('/admin/projects/filter', [ProjectController::class, 'filterProjects'])->name('admin.projects.filter');
Route::get('/projects/{project}/edit', [ProjectController::class, 'edit']);
Route::put('/projects/{project}', [ProjectController::class, 'update']);
Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

});
require __DIR__.'/auth.php';

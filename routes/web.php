<?php

use App\Http\Controllers\admin\FieldController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DynamicController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\admin\DashboardController;
use App\Http\Controllers\admin\EntityController;
use App\Http\Controllers\admin\ProjectsController;
use App\Http\Controllers\admin\FilamentController;
use App\Http\Controllers\admin\ApiController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::resource('dashboard',DashboardController::class);
    Route::resource('projects',ProjectsController::class);
    Route::resource('apis',ApiController::class);
    Route::resource('projects.entities',EntityController::class);
    Route::resource('projects.entities.fields',FieldController::class);

    Route::get('/project/{projectID}', [DynamicController::class, 'getProject']);
    Route::get('/project/{projectID}/entity/{tableName}', [DynamicController::class, 'getEntity']);
    Route::post('/project/{projectID}/entity/{tableName}', [DynamicController::class, 'storeEntity']);
    
    // Filament Admin Panel Routes
    Route::get('/admin/projects/{project}/dynamic', [FilamentController::class, 'redirectToFilament'])
        ->name('projects.filament');

});

Route::get('/check-filament', function () {
    return [
        'auth_check' => auth()->check(),
        'canAccessFilament' => auth()->user()?->canAccessFilament(),
    ];
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});





require __DIR__.'/auth.php';

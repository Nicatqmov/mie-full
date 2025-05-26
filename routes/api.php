<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AllController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::middleware('auth:sanctum')->group(function () {
    Route::get('/all-projects', [AllController::class, 'getAllProjects']);
    Route::get('/project', [AllController::class, 'getProject']);
    Route::get('/project/tables', [AllController::class, 'getProjectTables']);
    Route::get('/project/table', [AllController::class, 'getProjectTableData']);
});

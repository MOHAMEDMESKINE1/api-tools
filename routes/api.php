<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskItemController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('notes',NoteController::class);
Route::apiResource('contacts',ContactController::class);
Route::apiResource('calendars',CalendarController::class);
Route::apiResource('tasks',TaskController::class);
Route::apiResource('task_items',TaskItemController::class);

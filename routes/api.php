<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');

});
Route::apiResource('users', UserController::class);




Route::prefix('projects')->group(function () {
    // Create a new project
    Route::post('/', [ProjectController::class, 'store']);
    Route::put('/{project}', [ProjectController::class, 'update']);
    // Delete a project
    Route::delete('/{project}', [ProjectController::class, 'destroy']);
    
    // Optionally, you can add a route to get all projects or a single project
    // Get all projects
    Route::get('/', [ProjectController::class, 'index']);
    // Get a single project by ID
    Route::get('/{project}', [ProjectController::class, 'show']);});




    
    // Task Routes
    Route::post('/tasks', [TaskController::class, 'store']);
    Route::put('/tasks/{taskId}', [TaskController::class, 'update']);
    Route::patch('/tasks/{id}/status', [TaskController::class, 'updateStatus']);
    Route::post('/tasks/{taskId}/notes', action: [TaskController::class, 'addNoteToTask']);
    Route::get('/user/tasks', [TaskController::class, 'getUserTasks'])->middleware('auth');
    Route::post('/projects/{project}/users/{user}/contribution-hours', [ProjectController::class, 'updateContributionHours']);

    // Filtering Tasks by status and priority
    Route::get('/tasks', [TaskController::class, 'index']);

    // Getting the latest and oldest tasks in a project
    Route::get('/projects/{projectId}/tasks/latest', [TaskController::class, 'latestTask']);
    Route::get('/projects/{projectId}/tasks/oldest', [TaskController::class, 'oldestTask']);

    // Getting task with highest priority with a specific condition
    Route::get('/projects/{projectId}/tasks/highest-priority/{titleCondition}', [TaskController::class, 'highestPriorityTaskWithCondition']);


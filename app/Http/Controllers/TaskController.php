<?php

namespace App\Http\Controllers;
use App\Http\Requests\UpdateStatusRequest;

use App\Models\Project;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\TaskRequest;
use App\Services\TaskService;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\User;

class TaskController extends Controller
{
    protected $taskService;
    /**
     * Summary of __construct
     * @param \App\Services\TaskService $taskService
     */
    public function __construct(TaskService $taskService)
    {
        $this->middleware(middleware: 'auth:api');
        $this->taskService = $taskService;
    }
    /**
     * Summary of store
     * @param \App\Http\Requests\TaskRequest $request
     * @return JsonResponse|mixed
     */
    public function store(TaskRequest $request)
    {
        $task = $this->taskService->createTask($request->validated());
        return response()->json(['task' => $task, 'message' => 'Task created successfully!'], 201);
    }

    // Update a task (only manager can update tasks) 
    public function update(TaskUpdateRequest $request, $taskId): JsonResponse
    {
        try {
            $updatedTask = $this->taskService->updateTaskAndHandleNoteOrStatus(
                $request->validated(),
                $taskId
            );

            return response()->json([
                'success' => true,
                'message' => 'Task updated successfully.',
                'data' => $updatedTask
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }
    /**
     * Summary of addNoteToTask
     * @param \App\Http\Requests\TaskUpdateRequest $request
     * @param mixed $taskId
     * @return JsonResponse|mixed
     */
    public function addNoteToTask(TaskUpdateRequest $request, $taskId)
    {
        try {
            $validatedData = $request->validated();
            $updatedTask = $this->taskService->updateTaskAndHandleNoteOrStatus($validatedData, $taskId);

            return response()->json([
                'success' => true,
                'message' => 'Note added successfully.',
                'data' => $updatedTask
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    // Fetch all tasks filtered by status and priority
    public function index(Request $request)
    {
        $tasks = $this->taskService->getFilteredTasks($request->status, $request->priority);
        return response()->json(['tasks' => $tasks], 200);
    }

    // Fetch the latest task
    public function latestTask($projectId): JsonResponse
    {
        $project = Project::findOrFail($projectId);
        $latestTask = $project->tasks()->latest()->first();

        return response()->json($latestTask);
    }
    /**
     * fetch oldestTask
     * @param mixed $projectId
     * @return \Illuminate\Http\JsonResponse
     */
    public function oldestTask($projectId): JsonResponse
    {
        $project = Project::findOrFail($projectId);
        $oldestTask = $project->tasks()->oldest()->first();

        return response()->json($oldestTask);
    }

    // Fetch the task with the highest priority that matches a condition
    public function highestPriorityTaskWithCondition($projectId, $titleCondition)
    {
        $task = $this->taskService->getHighestPriorityTaskWithCondition($projectId, $titleCondition);
        return response()->json(['task' => $task], 200);
    }
    /**
     * Summary of getUserTasks
     * @return JsonResponse|mixed
     */
    public function getUserTasks()
    {
        $tasks = $this->taskService->getUserTasks();
        return response()->json($tasks);
    }
    /**
     * Summary of updateContributionHours
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Project $project
     * @param \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateContributionHours(Request $request, Project $project, User $user): JsonResponse
{
    dd($request);

    $this->validate($request, [
        'hours' => 'required|integer|min:1',
    ]);

    $hours = $request->input('hours');

    try {
        // Call the service method to update contribution hours
        $this->taskService->updateContributionHours($user, $project, $hours);

        return response()->json([
            'message' => 'Contribution hours updated successfully.',
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => $e->getMessage(),
        ], 403); // Return 403 Forbidden if not authorized
    }
}}


<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProjectController extends Controller
{
    protected $projectService;

    /**
     * Create a new controller instance.
     *
     * @param ProjectService $projectService
     */
    public function __construct(ProjectService $projectService)
    {
     $this->middleware(middleware: 'auth:api');
        $this->projectService = $projectService;
    }

    /**
     * Store a newly created project in storage.
     *
     * @param StoreProjectRequest $request
     * @return JsonResponse
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject($request->validated());
        return response()->json($project, Response::HTTP_CREATED);
    }

    /**
     * Update the specified project in storage.
     *
     * @param UpdateProjectRequest $request
     * @param Project $project
     * @return JsonResponse
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $project = $this->projectService->updateProject($project, $request->validated());
        return response()->json($project);
    }
    /**
     * Summary of index
     * @return JsonResponse|mixed
     */
    public function index()
    {
        // Use the service to get all projects
        $projects = $this->projectService->getAllProjects();

        // Return the response as JSON
        return response()->json($projects);
    }
    /**
     * Remove the specified project from storage.
     *
     * @param Project $project
     * @return JsonResponse
     */
    public function destroy(Project $project): JsonResponse
    {
        $this->projectService->deleteProject($project);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
    /**
     * Summary of updateContributionHours
     * @param \Illuminate\Http\Request $request
     * @param mixed $projectId
     * @param mixed $userId
     * @return JsonResponse|mixed
     */
    public function updateContributionHours(Request $request, $projectId, $userId)
    {
        $validated = $request->validate([
            'contribution_hours' => 'required|numeric|min:0',
        ]);

        $project = Project::findOrFail($projectId);

        // Update the contribution hours in the pivot table (project_user)
        $project->users()->updateExistingPivot($userId, [
            'contribution_hours' => $validated['contribution_hours'],
            'last_activity' => now(), // Optionally update the last activity time
        ]);

        return response()->json(['message' => 'Contribution hours updated successfully']);
    }
   
}


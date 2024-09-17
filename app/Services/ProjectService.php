<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProjectService
{
    /**
     * Create a new project.
     *
     * @param array $data
     * @return Project
     */
    public function createProject(array $data): Project
    {
        // Check user permissions
    $user = Auth::user();
    if ($user->role !== 'manager') {
        abort(403, 'Unauthorized');
    }

    // Create the project
    $project = Project::create([
        'name' => $data['name'],
        'description' => $data['description'] ?? null,
    ]);

    // Add team members to the project
    if (isset($data['users']) && is_array($data['users'])) {
        foreach ($data['users'] as $teamMember) {
            $project->users()->attach($teamMember['id'], [
                'role' => $teamMember['role'],
                'contribution_hours' => 0, // مبدئياً صفر
                'last_activity' => now(),
            ]);
        }
    }

    return $project;
    }

    /**
     * Update an existing project.
     *
     * @param Project $project
     * @param array $data
     * @return Project
     */
    public function updateProject(Project $project, array $data): Project
    {
        // Check user permissions
        $user = Auth::user();
        if (!$user->role === 'manager' && $user->id !== $project->user_id) {
            abort(403, 'Unauthorized');
        }

        $project->update($data);

        return $project;
    }

    /**
     * Delete an existing project.
     *
     * @param Project $project
     * @return void
     */
    public function deleteProject(Project $project): void
    {
        $user = Auth::user();

        // Correct permission check: if the user is not a manager and not the owner of the project
        if ($user->role !== 'manager' && $user->id !== $project->user_id) {
            abort(403, 'Unauthorized');
        }
    
        // Delete the project
        $project->delete();
    }
    /**
     * Summary of getAllProjects
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getAllProjects()
    {
        return Project::with(['tasks.user'])->get();

    }
   
}

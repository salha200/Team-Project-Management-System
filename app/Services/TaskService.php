<?php

namespace App\Services;

use App\Models\User;
use App\Models\Task;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TaskService
{
    /**
     * Summary of createTask
     * @param mixed $data
     * @throws \Exception
     * @return Task|\Illuminate\Database\Eloquent\Model
     */
    public function createTask($data)
    {
        $project = Project::findOrFail($data['project_id']);
        $user = Auth::user();
        $dueDate = Carbon::parse($data['due_date'])->format('Y-m-d H:i:s');

        // Ensure only manager can add tasks
        if ($user->role !== 'manager') {
            throw new \Exception('Unauthorized. Only a manager can create tasks.');
        }

        // الحصول على المستخدم المسند
        $assignedUser = User::findOrFail($data['assigned_user_id']);
        
        // Ensure the assigned user is either developer or tester
        if (!in_array($assignedUser->role, ['developer', 'tester'])) {
            throw new \Exception('Unauthorized. Task can only be assigned to a developer or tester.');
        }

        // إضافة المستخدم المسند إلى البيانات
        $data['due_date'] = $dueDate;

        // create task
        $task = Task::create($data);

        // إلحاق المهمة بالمستخدم مع تعيين الدور
        $assignedUser->tasks()->attach($task->id, ['role' => 'developer']); // Update this role if necessary
        if ($assignedUser->role === 'developer') {
            $this->updateContributionHours($assignedUser, $project, 1);}
        return $task;
    } public function updateContributionHours(User $user, Project $project, int $hours)
    {
        // Check if the authenticated user is an Admin
        $authUser = Auth::user();
        if ($authUser->role !== 'manager') {
            throw new \Exception('Unauthorized. Only an admin can update contribution hours.');
        }
    
        // Check if the user is part of the project
        if (!$project->users->contains($user)) {
            throw new \Exception('User is not associated with this project.');
        }
    
        // Update contribution hours
        $pivot = $project->users()->wherePivot('user_id', $user->id)->first()->pivot;
        $pivot->contribution_hours += $hours;
        $pivot->save();
    }
    
    public function updateTaskAndHandleNoteOrStatus($data, $taskId)
    {
        $user = Auth::user();
        $task = Task::findOrFail($taskId);

        switch ($user->role) {
            case 'manager':
                // Manager can update the entire task
                $this->updateTask($data, $task);
                break;

            case 'developer':
                // Developer can only update the status
                if (isset($data['status'])) {
                    $this->updateTaskStatus($data['status'], $task);
                }
                break;

            case 'tester':
                // Tester can only add notes
                if (isset($data['note'])) {
                    $this->addNoteToTask($data['note'], $task);
                }
                break;

            default:
                throw new \Exception('Unauthorized. Your role does not permit this action.');
        }

        return $task;
    }

    private function updateTask(array $data, Task $task)
    {
        // Ensure only manager can update tasks
        if (Auth::user()->role !== 'manager') {
            throw new \Exception('Unauthorized. Only a manager can update tasks.');
        }

        $task->update($data);
    }

    private function updateTaskStatus(string $status, Task $task)
    {
        // Ensure only developer can update task status
        if (Auth::user()->role !== 'developer') {
            throw new \Exception('Unauthorized. Only a developer can update task status.');
        }

        // Ensure the task is assigned to the current user
        if ($task->assigned_user_id !== Auth::id()) {
            throw new \Exception('Unauthorized. You can only update the status of tasks assigned to you.');
        }

        // Validate status transition
        $currentStatus = $task->status;
        $validTransitions = [
            'new' => ['in_progress'],
            'in_progress' => ['completed']
        ];

        if (!isset($validTransitions[$currentStatus]) || !in_array($status, $validTransitions[$currentStatus])) {
            throw new \Exception('Invalid status transition.');
        }

        $task->status = $status;
        $task->save();
    }

    private function addNoteToTask(string $note, Task $task)
    {
        // Ensure only tester can add notes
        if (Auth::user()->role !== 'tester') {
            throw new \Exception('Unauthorized. Only a tester can add notes.');
        }

        // Add note to task
        $task->note = $note; // تأكد من استخدام العمود الصحيح
        $task->save();
    }
   
    public function getFilteredTasks($status, $priority)
    {
        return Task::query()
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($priority, fn($query) => $query->where('priority', $priority))
            ->get();
    }

    public function getLatestTask($projectId)
    {
        $project = Project::findOrFail($projectId);

        return $project->tasks()->latestOfMany()->first();
    }

    public function getOldestTask($projectId)
    {
        $project = Project::findOrFail($projectId);

        return $project->tasks()->oldestOfMany()->first();
    }
    public function getHighestPriorityTaskWithCondition($projectId, $titleCondition)
    {
        $project = Project::findOrFail($projectId);
    
        return $project->tasks()
            ->where('title', 'LIKE', '%' . $titleCondition . '%') // تطبيق الشرط على العنوان
            ->orderBy('priority', 'desc') // ترتيب النتائج بناءً على الأولوية
            ->first(); // جلب أول نتيجة (ذات الأولوية القصوى)
    }
    

    public function getUserTasks()
    {
        $user = Auth::user(); // تأكد من أن المستخدم تم التحقق منه بشكل صحيح

        // الحصول على المهام للمستخدم من خلال العلاقة المعدلة
        $tasks = $user->tasks()->withPivot('role')->get();
    
        return $tasks;
    }
}

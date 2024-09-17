<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];
    /**
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'project_users')
                    ->withPivot('role', 'contribution_hours', 'last_activity');
    }
    /**
     * Summary of tasks
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    /**
     * Summary of latestTask
     * @return Model|object|\Illuminate\Database\Eloquent\Relations\HasMany|null
     */
    public function latestTask()
    {
        return $this->tasks()->latest()->first();
    }
    /**
     * Summary of oldestTask
     * @return Model|object|\Illuminate\Database\Eloquent\Relations\HasMany|null
     */
    public function oldestTask()
    {
        return $this->tasks()->oldest()->first();
    }
    /**
     * Summary of updateContributionHours
     * @param \App\Models\User $user
     * @param int $hours
     * @return void
     */
    public function updateContributionHours(User $user, int $hours): void
    {
        // Update the contribution_hours field in the pivot table
        $this->users()->updateExistingPivot($user->id, [
            'contribution_hours' => \DB::raw("coalesce(contribution_hours, 0) + $hours")
        ]);
    }
}

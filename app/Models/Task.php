<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'status', 'priority', 'due_date', 'project_id', 'assigned_user_id', 'note'];
    protected $casts = ['due_date' => 'datetime'];
    /**
     * Summary of users
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     * with poivit is role
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'task_user')->withPivot('role')->withTimestamps();
    }
    /**
     * Summary of project
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    /**
     * Summary of user
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
    /**
     * Summary of scopeStatus
     * @param mixed $query
     * @param mixed $status
     * @return mixed
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    /**
     * Summary of scopePriority
     * @param mixed $query
     * @param mixed $priority
     * @return mixed
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }
    /**
     * Summary of scopeHighestPriorityWithCondition
     * @param mixed $query
     * @param mixed $condition
     * @return mixed
     */
    public function scopeHighestPriorityWithCondition($query, $condition)
    {
        return $query->where('title', 'like', "%$condition%")
                     ->orderBy('priority', 'desc')
                     ->first();
    }
}

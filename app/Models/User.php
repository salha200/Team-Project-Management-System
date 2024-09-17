<?php
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Models\Project;
use App\Models\Task;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password'];
    protected $guarded = ['role'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_users')
                    ->withPivot('role', 'contribution_hours', 'last_activity');
    }

    public function tasksInProjects()
    {
        return $this->hasManyThrough(Task::class, Project::class, 'id', 'project_id', 'id', 'id')
                    ->where('assigned_user_id', $this->id);
    }
    

    public function hasRole($role)
    {
        return $this->role === $role;
    }
}

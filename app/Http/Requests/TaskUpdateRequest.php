<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TaskUpdateRequest extends FormRequest
{
    public function authorize()
    {
        $user = Auth::user();
        return in_array($user->role, ['manager', 'developer', 'tester']); // Only allow manager, developer, or tester
    }

    public function rules()
    {
        $user = Auth::user();

        switch ($user->role) {
            case 'manager':
                return [
                    'title' => 'sometimes|required|string|max:255',
                    'description' => 'sometimes|required|string',
                    'status' => 'sometimes|required|string',
                    'priority' => 'sometimes|required|in:low,medium,high',
                    'due_date' => 'sometimes|required|date',
                    'note' => 'nullable|string',
                ];
            case 'developer':
                return [
                    'status' => 'required|string|in:new,in_progress,completed',
                ];
            case 'tester':
                return [
                    'note' => 'required|string',
                ];
            default:
                return [];
        }
    }
}

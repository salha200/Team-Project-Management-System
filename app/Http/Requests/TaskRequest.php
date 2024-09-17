<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    public function authorize()
    {
        return true; // تأكد من أن المستخدم مصرح له
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:new,in_progress,completed',
            'priority' => 'required|string|in:low,medium,high',
            'due_date' => 'required|date',
             'project_id' => 'required|exists:projects,id',
            'assigned_user_id' => 'nullable|exists:users,id' // تأكد من أن هذا معرف المستخدم صحيح
        ];
    }
}

<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskFilterRequest extends FormRequest
{
    public function authorize()
    {
        // Authorization logic can be managed here (true for now)
        return true;
    }

    public function rules()
    {
        return [
            'status' => 'nullable|string|in:new,in progress,completed',
            'priority' => 'nullable|string|in:low,medium,high',
        ];
    }

    public function messages()
    {
        return [
            'status.in' => 'Status must be one of the following: new, in progress, completed.',
            'priority.in' => 'Priority must be one of the following: low, medium, high.',
        ];
    }
}

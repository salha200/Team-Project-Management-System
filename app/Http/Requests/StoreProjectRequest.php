<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return auth()->user()->hasRole('manager');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'users' => 'required|array',
            'users.*.id' => 'required|exists:users,id',
            'users.*.role' => 'required|string|in:manager,developer,tester',
        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => 'The project name is required.',
            'name.string' => 'The project name must be a string.',
            'name.max' => 'The project name may not be greater than :max characters.',
            'users.required' => 'You must assign at least one user to the project.',
            'users.array' => 'The users field must be an array.',
            'users.*.id.required' => 'Each user must have a valid ID.',
            'users.*.id.exists' => 'One or more user IDs do not exist.',
            'users.*.role.required' => 'Each user must have a specified role.',
            'users.*.role.in' => 'The role must be one of the following: manager, developer, tester.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'name' => 'project name',
            'description' => 'project description',
            'users' => 'assigned users',
            'users.*.id' => 'user ID',
            'users.*.role' => 'user role',
        ];
    }

}

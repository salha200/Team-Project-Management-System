<?php

namespace App\Http\Requests\Controllers;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;

/**
 * Class UserRequest
 *
 * @package App\Http\Requests
 */
class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:developer,manager,tester',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'Full Name',
            'email' => 'Email Address',
            'password' => 'Password',
            'role' => 'User Role',
        ];
    }

    /**
     * Get custom validation error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The :attribute field is required.',
            'email.required' => 'The :attribute field is required.',
            'email.email' => 'The :attribute must be a valid email address.',
            'email.unique' => 'This :attribute is already taken.',
            'password.required' => 'The :attribute field is required.',
            'password.min' => 'The :attribute must be at least :min characters.',
            'password.confirmed' => 'The password confirmation does not match.',
            'role.required' => 'The :attribute field is required.',
            'role.in' => 'The :attribute must be one of the following values: admin, manager, user.',
        ];
    }

    /**
     * Perform any actions after successful validation.
     *
     * @return void
     */
    protected function passedValidation()
    {
        Log::info('User Request Validation Successful');
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();
        Log::error('User Request Validation Failed:', ['errors' => $errors]);
        throw new \Exception(json_encode($errors));
    }
}

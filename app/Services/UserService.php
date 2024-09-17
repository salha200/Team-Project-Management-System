<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserService
{
    use AuthorizesRequests;

    /**
     * Get all users.
     *
     * @return Collection
     */
    public function getAllUsers(): Collection
    {
        try {
            $user = auth()->user();
            
            // Check if the user is a manager
            if ($user->role === 'manager') {
                // If the user is a manager, allow viewing all users
                return User::all();
            }
            
       
            
            return User::all();
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to retrieve users'], 500);
        }
    }
    

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data)
{
    try {
        $this->authorize('create', User::class);

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password']),
        'role' => $data['role'] ?? 'developer',
    ]);

    return $user;
} catch (Exception $e) {
    return response()->json(['error' => "$e"], 500);
}
}
    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        try {
            // الحصول على المستخدم الحالي
            $currentUser = auth()->user();
    
            // تسجيل اللوجات للتحقق من البيانات
            \Log::info('Current user:', ['id' => $currentUser->id, 'role' => $currentUser->role]);
            \Log::info('User to update:', ['id' => $user->id, 'role' => $user->role]);
    
            // تحقق من أن المستخدم هو مدير
            if ($currentUser->role !== 'manager') {
                abort(403, 'Unauthorized: Only managers can update users.');
            }
    
            // تحقق من أن المدير يملك الصلاحية لتحديث هذا المستخدم
           
            // تحديث المستخدم
            $user->update($data);
    
            return $user;
    
        } catch (Exception $e) {
            // التعامل مع الأخطاء
            return response()->json(['error' => 'Failed to update user', 'exception' => $e->getMessage()], 500);
        }
    }
    

    /**
     * Delete a user.
     *
     * @param User $user
     * @return bool
     */
    public function deleteUser(User $user): JsonResponse
{
    try {
        $currentUser = auth()->user();

        // Check if the current user is a manager
        if ($currentUser->role === 'manager') {
            // Authorize deletion

            // Perform deletion
            $user->delete();

            return response()->json(null, 204);
        }

        // If the user is not a manager, return unauthorized response
        return response()->json(['error' => 'Unauthorized'], 403);
    } catch (Exception $e) {
        // Return error response in case of exceptions
        return response()->json(['error' => 'Failed to delete user'], 500);
    }
}

}
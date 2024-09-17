<?php

namespace App\Http\Controllers;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Exception;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UpdateUserRequest;


class UserController extends Controller
{
    protected $userService;
    use AuthorizesRequests;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the users.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
       
            $users = $this->userService->getAllUsers();
            return response()->json($users);
       
    }

    /**
     * Store a newly created user in storage.
     *
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function store(UserRequest $request): JsonResponse
    {
        
            $user = $this->userService->createUser($request->validated());
            return response()->json($user, 201);
       
    }

    /**
     * Update the specified user in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
      
            $updatedUser = $this->userService->updateUser($user, $request->validated());
            return response()->json($updatedUser);
       
    }

    public function show(User $user)
        {}
    /**
     * Remove the specified user from storage.
     *
     * @param User $user
     * @return JsonResponse
     */
    public function destroy(User $user): JsonResponse
    {
            $this->userService->deleteUser($user);
            return response()->json(null, 204);   
    }
    
    public function restoreUser($id){
        $User = User::onlyTrashed()->find($id);
        if ($User) {
            $User->restore();
            return response()->json(['message' => 'User restored successfully']);
        }
        return response()->json(['message' => 'User not found'], 404);
    }
    public function forceDelete(User $user): JsonResponse{
        try {
           $user=User::withTrashed()->find($user->id);
           $user->forceDelete();
           return response()->json(null, 204);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete user'], 500);
        }
    }
   
    

        

}
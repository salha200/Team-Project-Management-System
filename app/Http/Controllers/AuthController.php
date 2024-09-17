<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
        $this->authService = $authService;
    }

    /**
     * Handle login request.
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $loginData = $this->authService->login($credentials);

        if (!$loginData) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'user' => $loginData['user'],
            'authorisation' => [
                'token' => $loginData['token'],
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * Handle registration request.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $registerData = $this->authService->register($data);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $registerData['user'],
            'authorisation' => [
                'token' => $registerData['token'],
                'type' => 'bearer',
            ]
        ], 201);
    }

    /**
     * Handle logout request.
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Handle refresh token request.
     *
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $refreshData = $this->authService->refresh();

        return response()->json([
            'status' => 'success',
            'user' => $refreshData['user'],
            'authorisation' => [
                'token' => $refreshData['token'],
                'type' => 'bearer',
            ]
        ]);
    }
}

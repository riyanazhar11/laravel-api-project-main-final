<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthService;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\SignupRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Register a new user and create company
     */
    public function register(SignupRequest $request): JsonResponse
    {
        $result = $this->authService->signup($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully. Company created. Please check your email to verify your account.',
            'data' => [
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'email_verified' => $result['user']->isEmailVerified(),
                    'company_id' => $result['user']->company_id,
                ],
                'company' => [
                    'id' => $result['company']->id,
                    'name' => $result['company']->name,
                    'owner_id' => $result['company']->owner_id
                ]
            ]
        ], 201);
    }

    /**
     * Authenticate user and generate token
     */
    public function authenticate(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'token' => $result['user']->api_token,
            'email_verified' => true
        ]);
    }

    /**
     * Logout user by clearing token
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->bearerToken());

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(string $token): JsonResponse
    {
        $this->authService->verifyEmail($token);

        return response()->json([
            'success' => true,
            'message' => 'Email verified successfully'
        ]);
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request): JsonResponse
    {
        $this->authService->resendVerification($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Verification email sent successfully'
        ]);
    }

    /**
     * Send password reset email
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $this->authService->forgotPassword($request->email);

        return response()->json([
            'success' => true,
            'message' => 'Password reset email sent.'
        ]);
    }

    /**
     * Reset password with token
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed'
        ]);

        $this->authService->resetPassword($request->token, $request->password);

        return response()->json([
            'success' => true, 
            'message' => 'Password has been reset successfully.'
        ]);
    }
}

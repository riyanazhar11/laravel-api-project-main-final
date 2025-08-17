<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Models\CompanyInvitation;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AuthService
{
    /**
     * Register a new user and create company
     */
    public function signup(array $data): array
    {
        return DB::transaction(function () use ($data) {
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'email_verification_token' => Str::random(64),
                'email_verification_token_expires_at' => now()->addHours(24),
            ]);

            // Create company
            $company = Company::create([
                'name' => $user->name . "'s Company",
                'owner_id' => $user->id,
            ]);

            // Assign company to user
            $user->update(['company_id' => $company->id]);

            // Send verification email
            $this->sendVerificationEmail($user);

            return [
                'user' => $user->fresh(),
                'company' => $company
            ];
        });
    }

    /**
     * Authenticate user and generate token
     */
    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new UnauthorizedException('Invalid credentials');
        }

        if (!$user->isEmailVerified()) {
            throw new UnauthorizedException('Please verify your email address before logging in');
        }

        // Generate new API token
        $user->update(['api_token' => Str::random(60)]);

        return ['user' => $user->fresh()];
    }

    /**
     * Logout user by clearing token
     */
    public function logout(string $token): void
    {
        $user = User::where('api_token', $token)->first();
        
        if ($user) {
            $user->update(['api_token' => null]);
        }
    }

    /**
     * Verify email with token
     */
    public function verifyEmail(string $token): void
    {
        $user = User::where('email_verification_token', $token)
                    ->where('email_verification_token_expires_at', '>', now())
                    ->first();

        if (!$user) {
            throw new ResourceNotFoundException('Invalid or expired verification token');
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
            'email_verification_token_expires_at' => null,
        ]);
    }

    /**
     * Resend verification email
     */
    public function resendVerification(User $user): void
    {
        if ($user->isEmailVerified()) {
            throw new UnauthorizedException('Email is already verified');
        }

        $user->update([
            'email_verification_token' => Str::random(64),
            'email_verification_token_expires_at' => now()->addHours(24),
        ]);

        $this->sendVerificationEmail($user);
    }

    /**
     * Send password reset email
     */
    public function forgotPassword(string $email): void
    {
        $token = Str::random(64);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $token,
                'created_at' => now()
            ]
        );

        $resetUrl = url("/reset-password/{$token}");

        Mail::send('emails.password_reset', ['url' => $resetUrl], function ($message) use ($email) {
            $message->to($email);
            $message->subject('Reset Your Password');
        });
    }

    /**
     * Reset password with token
     */
    public function resetPassword(string $token, string $password): void
    {
        $resetRecord = DB::table('password_resets')
            ->where('token', $token)
            ->first();

        if (!$resetRecord) {
            throw new ResourceNotFoundException('Invalid token');
        }

        $user = User::where('email', $resetRecord->email)->first();

        if (!$user) {
            throw new ResourceNotFoundException('User not found');
        }

        $user->update(['password' => Hash::make($password)]);

        DB::table('password_resets')->where('email', $resetRecord->email)->delete();
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail(User $user): void
    {
        $verificationUrl = url("/api/verify-email/{$user->email_verification_token}");
        
        Mail::send('emails.verify', [
            'user' => $user,
            'verificationUrl' => $verificationUrl
        ], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify Your Email Address');
        });
    }
}

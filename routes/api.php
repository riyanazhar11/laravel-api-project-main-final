<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ChannelController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Authentication routes
Route::post('/signup', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth.api');
Route::get('/verify-email/{token}', [AuthController::class, 'verifyEmail']);
Route::post('/resend-verification', [AuthController::class, 'resendVerification'])->middleware('auth.api');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
// Company invitation routes
Route::post('/invite-employee', [CompanyController::class, 'inviteEmployee'])->middleware(['auth.api', 'company.owner']);
Route::match(['GET', 'POST'], '/accept-invitation/{token}', [CompanyController::class, 'acceptInvitation']);
Route::get('/invitations', [CompanyController::class, 'listInvitations'])->middleware(['auth.api', 'company.owner']);
Route::delete('/invitations/{invitation}', [CompanyController::class, 'cancelInvitation'])->middleware(['auth.api', 'company.owner']);

// Employee management routes
Route::delete('/employees/{employee}', [CompanyController::class, 'removeEmployee'])->middleware(['auth.api', 'company.owner']);
// Route::middleware('auth:api')->group(function () {
//     Route::post('/company/add-employee', [CompanyController::class, 'addEmployee']);
// });

// Channel routes
Route::post('/channels', [ChannelController::class, 'createChannel'])->middleware('auth.api');
Route::get('/channels', [ChannelController::class, 'listChannels'])->middleware('auth.api');
Route::get('/channels/{channel}', [ChannelController::class, 'getChannelDetails'])->middleware(['auth.api', 'channel.member']);
Route::post('/channels/{channel}/invite', [ChannelController::class, 'inviteUserToChannel'])->middleware(['auth.api', 'channel.member']);
Route::match(['GET', 'POST'], '/accept-channel-invitation/{token}', [ChannelController::class, 'acceptChannelInvitation']);
Route::delete('/channels/{channel}/leave', [ChannelController::class, 'leaveChannel'])->middleware(['auth.api', 'channel.member']);
Route::delete('/channels/{channel}', [ChannelController::class, 'deleteChannel'])->middleware(['auth.api', 'channel.admin']);
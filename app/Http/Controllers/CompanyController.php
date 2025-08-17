<?php

namespace App\Http\Controllers;

use App\Services\CompanyService;
use App\Http\Requests\Company\InviteEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    public function __construct(
        private CompanyService $companyService
    ) {}

    /**
     * Invite employee to company
     */
    public function inviteEmployee(InviteEmployeeRequest $request): JsonResponse
    {
        $invitation = $this->companyService->inviteEmployee($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Invitation sent successfully',
            'data' => [
                'invitation_id' => $invitation->id,
                'email' => $invitation->email,
                'expires_at' => $invitation->expires_at
            ]
        ]);
    }

    /**
     * Remove employee from company
     */
    public function removeEmployee(Request $request, int $employeeId): JsonResponse
    {
        $this->companyService->removeEmployee($request->user(), $employeeId);

        return response()->json([
            'success' => true, 
            'message' => 'Employee removed successfully'
        ]);
    }

    /**
     * Accept company invitation
     */
    public function acceptInvitation(Request $request, string $token): JsonResponse
    {
        $password = $request->isMethod('POST') ? $request->password : null;
        $result = $this->companyService->acceptInvitation($token, $password);

        $message = $request->isMethod('GET') 
            ? 'Invitation accepted successfully! You are now an employee of ' . $result['user']->company->name . '. Please check your email for login credentials.'
            : 'Invitation accepted successfully. You are now an employee of ' . $result['user']->company->name;

        $response = [
            'success' => true,
            'message' => $message,
            'data' => [
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'email' => $result['user']->email,
                    'company_id' => $result['user']->company_id,
                ],
                'token' => $result['user']->api_token
            ]
        ];

        // Include password in response for GET requests (auto-generated)
        if ($request->isMethod('GET')) {
            $response['data']['password'] = $result['password'];
        }

        return response()->json($response);
    }

    /**
     * List pending invitations
     */
    public function listInvitations(Request $request): JsonResponse
    {
        $invitations = $this->companyService->listInvitations($request->user());

        return response()->json([
            'success' => true,
            'data' => $invitations
        ]);
    }

    /**
     * Cancel invitation
     */
    public function cancelInvitation(Request $request, int $invitationId): JsonResponse
    {
        $this->companyService->cancelInvitation($request->user(), $invitationId);

        return response()->json([
            'success' => true,
            'message' => 'Invitation cancelled successfully'
        ]);
    }
}

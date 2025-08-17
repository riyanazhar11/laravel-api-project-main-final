<?php

namespace App\Http\Controllers;

use App\Services\ChannelService;
use App\Http\Requests\Channel\CreateChannelRequest;
use App\Http\Requests\Channel\InviteToChannelRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChannelController extends Controller
{
    public function __construct(
        private ChannelService $channelService
    ) {}

    /**
     * Create a new channel
     */
    public function createChannel(CreateChannelRequest $request): JsonResponse
    {
        $channel = $this->channelService->createChannel($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Channel created successfully',
            'data' => [
                'channel' => [
                    'id' => $channel->id,
                    'name' => $channel->name,
                    'description' => $channel->description,
                    'type' => $channel->type,
                    'created_by' => $request->user()->name,
                    'created_at' => $channel->created_at
                ]
            ]
        ], 201);
    }

    /**
     * List channels visible to user
     */
    public function listChannels(Request $request): JsonResponse
    {
        $channels = $this->channelService->getVisibleChannels($request->user());

        return response()->json([
            'success' => true,
            'data' => $channels->map(function ($channel) use ($request) {
                $isMember = $channel->members()->where('user_id', $request->user()->id)->exists();
                return [
                    'id' => $channel->id,
                    'name' => $channel->name,
                    'description' => $channel->description,
                    'type' => $channel->type,
                    'created_by' => $channel->creator->name,
                    'is_member' => $isMember,
                    'member_count' => $channel->members()->count(),
                    'created_at' => $channel->created_at
                ];
            })
        ]);
    }

    /**
     * Get channel details
     */
    public function getChannelDetails(Request $request, int $channelId): JsonResponse
    {
        $result = $this->channelService->getChannelDetails($request->user(), $channelId);

        return response()->json([
            'success' => true,
            'data' => [
                'channel' => [
                    'id' => $result['channel']->id,
                    'name' => $result['channel']->name,
                    'description' => $result['channel']->description,
                    'type' => $result['channel']->type,
                    'created_by' => $result['channel']->creator->name,
                    'is_member' => $result['is_member'],
                    'member_count' => $result['members']->count(),
                    'created_at' => $result['channel']->created_at
                ],
                'members' => $result['members']->map(function ($member) {
                    return [
                        'id' => $member->user->id,
                        'name' => $member->user->name,
                        'email' => $member->user->email,
                        'role' => $member->role,
                        'joined_at' => $member->joined_at
                    ];
                })
            ]
        ]);
    }

    /**
     * Invite user to channel
     */
    public function inviteUserToChannel(InviteToChannelRequest $request, int $channelId): JsonResponse
    {
        $invitation = $this->channelService->inviteUserToChannel(
            $request->user(), 
            $channelId, 
            $request->validated()['user_id']
        );

        return response()->json([
            'success' => true,
            'message' => 'Invitation sent successfully',
            'data' => [
                'invitation_id' => $invitation->id,
                'invited_user' => [
                    'id' => $invitation->invitedUser->id,
                    'name' => $invitation->invitedUser->name,
                    'email' => $invitation->invitedUser->email
                ],
                'expires_at' => $invitation->expires_at
            ]
        ]);
    }

    /**
     * Accept channel invitation
     */
    public function acceptChannelInvitation(string $token): JsonResponse
    {
        $result = $this->channelService->acceptChannelInvitation($token);

        return response()->json([
            'success' => true,
            'message' => 'You have successfully joined ' . $result['channel']->name,
            'data' => [
                'channel' => [
                    'id' => $result['channel']->id,
                    'name' => $result['channel']->name,
                    'type' => $result['channel']->type
                ]
            ]
        ]);
    }

    /**
     * Leave channel
     */
    public function leaveChannel(Request $request, int $channelId): JsonResponse
    {
        $this->channelService->leaveChannel($request->user(), $channelId);

        return response()->json([
            'success' => true,
            'message' => 'You have left the channel successfully'
        ]);
    }

    /**
     * Delete channel
     */
    public function deleteChannel(Request $request, int $channelId): JsonResponse
    {
        $this->channelService->deleteChannel($request->user(), $channelId);

        return response()->json([
            'success' => true,
            'message' => 'Channel deleted successfully'
        ]);
    }
}

<?php

namespace App\Services;

use App\Models\Channel;
use App\Models\ChannelMember;
use App\Models\ChannelInvitation;
use App\Models\User;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ResourceNotFoundException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ChannelService
{
    /**
     * Create a new channel
     */
    public function createChannel(User $user, array $data): Channel
    {
        $channel = Channel::create([
            'company_id' => $user->company_id,
            'created_by' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
        ]);

        // Add creator as admin member
        ChannelMember::create([
            'channel_id' => $channel->id,
            'user_id' => $user->id,
            'role' => 'admin',
        ]);

        return $channel;
    }

    /**
     * Get channels visible to user
     */
    public function getVisibleChannels(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Channel::where('company_id', $user->company_id)
            ->where('is_active', true)
            ->get()
            ->filter(function ($channel) use ($user) {
                return $channel->isVisibleTo($user);
            })
            ->values();
    }

    /**
     * Get channel details
     */
    public function getChannelDetails(User $user, int $channelId): array
    {
        $channel = Channel::where('id', $channelId)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$channel) {
            throw new ResourceNotFoundException('Channel not found');
        }

        if (!$channel->isVisibleTo($user)) {
            throw new UnauthorizedException('Access denied to this channel');
        }

        $isMember = $channel->members()->where('user_id', $user->id)->exists();
        $members = $channel->members()->with('user')->get();

        return [
            'channel' => $channel,
            'is_member' => $isMember,
            'members' => $members
        ];
    }

    /**
     * Invite user to channel
     */
    public function inviteUserToChannel(User $inviter, int $channelId, int $invitedUserId): ChannelInvitation
    {
        $channel = Channel::where('id', $channelId)
            ->where('company_id', $inviter->company_id)
            ->first();

        if (!$channel) {
            throw new ResourceNotFoundException('Channel not found');
        }

        // Check if user can invite (must be member or admin)
        $userMembership = $channel->members()->where('user_id', $inviter->id)->first();
        if (!$userMembership) {
            throw new UnauthorizedException('You must be a member to invite others');
        }

        $invitedUser = User::where('id', $invitedUserId)
            ->where('company_id', $inviter->company_id)
            ->first();

        if (!$invitedUser) {
            throw new ResourceNotFoundException('User not found in your company');
        }

        // Check if user is already a member
        if ($channel->members()->where('user_id', $invitedUser->id)->exists()) {
            throw new UnauthorizedException('User is already a member of this channel');
        }

        // Check if invitation already exists
        $existingInvitation = ChannelInvitation::where('channel_id', $channel->id)
            ->where('invited_user_id', $invitedUser->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            throw new UnauthorizedException('Invitation already sent to this user');
        }

        // Create invitation
        $invitation = ChannelInvitation::create([
            'channel_id' => $channel->id,
            'invited_by' => $inviter->id,
            'invited_user_id' => $invitedUser->id,
            'invitation_token' => Str::random(64),
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // Send invitation email
        $this->sendChannelInvitationEmail($invitation);

        return $invitation;
    }

    /**
     * Accept channel invitation
     */
    public function acceptChannelInvitation(string $token): array
    {
        $invitation = ChannelInvitation::where('invitation_token', $token)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            throw new ResourceNotFoundException('Invalid or expired invitation');
        }

        // Check if user is already a member
        if ($invitation->channel->members()->where('user_id', $invitation->invited_user_id)->exists()) {
            throw new UnauthorizedException('You are already a member of this channel');
        }

        // Add user to channel
        ChannelMember::create([
            'channel_id' => $invitation->channel_id,
            'user_id' => $invitation->invited_user_id,
            'role' => 'member',
        ]);

        // Update invitation status
        $invitation->update(['status' => 'accepted']);

        return [
            'channel' => $invitation->channel,
            'user' => $invitation->invitedUser
        ];
    }

    /**
     * Leave channel
     */
    public function leaveChannel(User $user, int $channelId): void
    {
        $channel = Channel::where('id', $channelId)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$channel) {
            throw new ResourceNotFoundException('Channel not found');
        }

        $membership = $channel->members()->where('user_id', $user->id)->first();

        if (!$membership) {
            throw new UnauthorizedException('You are not a member of this channel');
        }

        // Prevent channel creator from leaving (they can only delete the channel)
        if ($channel->created_by === $user->id) {
            throw new UnauthorizedException('Channel creator cannot leave. You can delete the channel instead.');
        }

        $membership->delete();
    }

    /**
     * Delete channel
     */
    public function deleteChannel(User $user, int $channelId): void
    {
        $channel = Channel::where('id', $channelId)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$channel) {
            throw new ResourceNotFoundException('Channel not found');
        }

        // Only channel creator can delete
        if ($channel->created_by !== $user->id) {
            throw new UnauthorizedException('Only channel creator can delete the channel');
        }

        $channel->update(['is_active' => false]);
    }

    /**
     * Send channel invitation email
     */
    private function sendChannelInvitationEmail(ChannelInvitation $invitation): void
    {
        $acceptUrl = url("/api/accept-channel-invitation/{$invitation->invitation_token}");
        
        Mail::send('emails.channel_invitation', [
            'invitation' => $invitation,
            'acceptUrl' => $acceptUrl
        ], function ($message) use ($invitation) {
            $message->to($invitation->invitedUser->email);
            $message->subject('You\'ve been invited to join ' . $invitation->channel->name);
        });
    }
}

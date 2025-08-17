<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Channel;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ResourceNotFoundException;

class ChannelMemberMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $channelId = $request->route('channel') ?? $request->route('channelId');

        $channel = Channel::where('id', $channelId)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$channel) {
            throw new ResourceNotFoundException('Channel not found');
        }

        if (!$channel->isVisibleTo($user)) {
            throw new UnauthorizedException('Access denied to this channel');
        }

        $request->attributes->set('channel', $channel);
        return $next($request);
    }
}

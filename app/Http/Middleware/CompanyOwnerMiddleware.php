<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException;

class CompanyOwnerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->company_id || $user->company->owner_id !== $user->id) {
            throw new UnauthorizedException('Only company owner can perform this action');
        }

        return $next($request);
    }
}

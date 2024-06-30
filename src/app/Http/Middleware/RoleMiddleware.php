<?php

namespace App\Http\Middleware;

use App\Enums\EPermissionScope;
use App\Exceptions\UnauthorizedException;
use App\Util\Constant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$allowedScopes): Response
    {
        $user = auth()->user();

        if (is_null($user)) {
            throw new UnauthorizedException();
        }
        $role = $user->role_id;
        if (!array_key_exists($role->value, Constant::ROLE_SCOPES)) {
            throw new UnauthorizedException();
        }
        $scopes = Constant::ROLE_SCOPES[$role->value];

        $exists = array_filter($allowedScopes, fn ($allowedScope) => in_array(EPermissionScope::from($allowedScope), $scopes));
        if (count($exists) === 0) {
            throw new UnauthorizedException();
        }

        return $next($request);
    }
}

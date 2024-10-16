<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use App\Http\Services\AuthService;
use App\Http\Services\UserService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserSessionAuthenticationMiddleware
{
    public function __construct(private UserService $service)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        $deviceId = $request->header("x-device-id");

        $browser = $this->service->getUserWebBrowser($user, [ "device_id" => $deviceId ]);

        if (is_null($browser)) {
            throw new UnauthorizedException();
        }

        $jti = JWTAuth::getPayload(JWTAuth::getToken())->get("jti") ?? "-";

        $session = $this->service->getUserSession($user, [ "web_browser_id" => $browser->id, "access_token" => $jti ]);

        if (is_null($session)) {
            throw new UnauthorizedException();
        }

        return $next($request);
    }
}

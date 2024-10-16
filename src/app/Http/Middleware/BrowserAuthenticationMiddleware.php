<?php

namespace App\Http\Middleware;

use App\Exceptions\UnauthorizedException;
use App\Http\Services\AuthService;
use App\Http\Services\UserService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BrowserAuthenticationMiddleware
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

        return $next($request);
    }
}

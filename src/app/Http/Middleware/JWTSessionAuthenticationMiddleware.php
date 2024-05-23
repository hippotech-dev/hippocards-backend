<?php

namespace App\Http\Middleware;

use App\Exceptions\AppException;
use App\Exceptions\UnauthorizedException;
use App\Http\Services\UserService;
use App\Util\Constant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JWTSessionAuthenticationMiddleware
{
    public function __construct()
    {

    }
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $request->session()->get(Constant::JWT_TOKEN_KEY, null);

        if (is_null($accessToken)) {
            throw new UnauthorizedException();
        }

        try {
            $user = auth()->setToken($accessToken)->user();

            $request->merge([
                "user" => $user
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is absent'], 401);
        }

        return $next($request);
    }
}

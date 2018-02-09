<?php

namespace App\Http\Middleware;

use Closure;
use JWTAuth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param $type
     * @return mixed
     */
    public function handle($request, Closure $next, $type)
    {
        if (($type == 'user' && JWTAuth::getPayload()['auth_type'] == 'user') ||
            ($type == 'api_new_student' && JWTAuth::getPayload()['auth_type'] == 'api_new_student')) {
            return $next($request);
        }
        return response()->json(['message' => 'Unauthorized.', 'status_code' => 401], 401);
    }
}

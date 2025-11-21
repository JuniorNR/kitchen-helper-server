<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Enums\ApiCode;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'code' => ApiCode::UNAUTHENTICATED->value,
            ], 401);
        }
        if (!in_array($user->role, $roles, true)) {
            return response()->json([
                'code' => ApiCode::ACCESS_DENIED->value,
                'required_roles' => $roles,
                'current_role' => $user->role,
            ], 403);
        }
        return $next($request);
    }
}

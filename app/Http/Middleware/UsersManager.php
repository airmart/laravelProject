<?php

namespace App\Http\Middleware;

use App\Constants\RoleConstants;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UsersManager
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();

        $isAdmin = $user->role->access_level == RoleConstants::ADMIN_ACCESS_LEVEL;

        $isRouteWithId = $request->route()->named('users.update')
            || $request->route()->named('users.destroy');

        if ($isAdmin || $isRouteWithId && $user->id == $request->route('id')) {
            return $next($request);
        }

        return new JsonResponse(['error' => 'You do not have access to this links'], Response::HTTP_FORBIDDEN);
    }
}

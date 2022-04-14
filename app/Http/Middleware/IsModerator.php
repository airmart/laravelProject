<?php

namespace App\Http\Middleware;

use App\Constants\RoleConstants;
use App\Models\User;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class IsModerator
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->role()->get()[0]['role_name'] == RoleConstants::MODERATOR_ROLE_NAME) {
            return $next($request);
        }

        return new JsonResponse(['error' => 'You are not a moderator'], Response::HTTP_FORBIDDEN);
    }
}

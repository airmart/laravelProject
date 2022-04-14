<?php

namespace App\Http\Middleware;

use App\Constants\RoleConstants;
use App\Models\User;
use App\Repositories\UserRepository;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /** @var UserRepository  */
    public UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $next($request);
        }

        return new JsonResponse(['error' => 'You are not an admin'], Response::HTTP_FORBIDDEN);
    }
}

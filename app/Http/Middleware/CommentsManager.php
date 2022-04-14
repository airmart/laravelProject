<?php

namespace App\Http\Middleware;

use App\Models\Comment;
use App\Models\User;
use App\Repositories\CommentRepository;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CommentsManager
{
    /** @var CommentRepository */
    public CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

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

        $commentId = (int)$request->route('id');

        /** @var Comment $comment */
        $comment = $this->commentRepository->find($commentId);

        if ($user->role->access_level > 1 || $comment->user_id == $user->id) {
            return $next($request);
        }

        return new JsonResponse(['error' => 'You do not have access to this comment'], Response::HTTP_FORBIDDEN);
    }
}

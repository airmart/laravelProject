<?php

namespace App\Http\Middleware;

use App\Models\Post;
use App\Models\User;
use App\Repositories\PostRepository;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PostsManager
{
    /** @var PostRepository */
    public PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
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

        $postId = (int)$request->route('id');

        /** @var Post $post */
        $post = $this->postRepository->find($postId);

        if ($user->role->access_level > 1 || $post->user_id == $user->id) {
            return $next($request);
        }

        return new JsonResponse(['error' => 'You do not have access to this post'], Response::HTTP_FORBIDDEN);
    }
}

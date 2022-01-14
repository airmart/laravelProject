<?php

namespace App\Http\Controllers;

use App\Http\Repositories\PostRepository;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /** @var PostRepository */
    public PostRepository $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $posts = $this->postRepository->get();

        return new JsonResponse($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $post = $this->postRepository->create([
            'title' => $request->input('title'),
            'text' => $request->input('text'),
            'user_id' => $request->input('user_id')
        ]);

        return new JsonResponse($post);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $post = $this->postRepository->find($id);

        if (is_null($post)) {
            return new JsonResponse(['error' => 'Post does not exist'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $post = $this->postRepository->find($id);

        if (is_null($post)) {
            return new JsonResponse(['error' => 'Post does not exist'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id']
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->postRepository->update($id, [
            'title' => $request->input('title'),
            'text' => $request->input('text'),
            'user_id' => $request->input('user_id')
        ]);
        $post->refresh();

        return new JsonResponse($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $post = $this->postRepository->find($id);

        if (is_null($post)) {
            return new JsonResponse(['error' => 'Post does not exist'], Response::HTTP_NOT_FOUND);
        }

        $this->postRepository->delete($id);

        return new JsonResponse(['success' => true]);
    }
}

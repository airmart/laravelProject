<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Repositories\CommentRepository;
use App\Rules\AllowedRelationsRule;
use App\Rules\AllowedSortFieldsRule;
use App\Rules\ArrayKeyRule;
use App\Rules\IsArrayOfArraysRule;
use App\Rules\IsStringsArrayRule;
use App\Rules\IsUniqueArrayRule;
use App\Rules\SortDirectionAllowedRule;
use App\Services\PaginationHelper;
use App\Services\SortDataParser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    /** @var CommentRepository */
    private CommentRepository $commentRepository;

    /** @var PaginationHelper */
    private PaginationHelper $paginationHelper;

    /** @var SortDataParser */
    private SortDataParser $sortDataParser;

    public function __construct(
        CommentRepository $commentRepository,
        PaginationHelper $paginationHelper,
        SortDataParser $sortDataParser
    )
    {
        $this->commentRepository = $commentRepository;
        $this->paginationHelper = $paginationHelper;
        $this->sortDataParser = $sortDataParser;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'sort' => [
                'bail',
                'array',
                new IsArrayOfArraysRule(),
                new ArrayKeyRule(['sortDirection', 'sortField']),
                new SortDirectionAllowedRule(),
                new AllowedSortFieldsRule($this->commentRepository)
            ],
            'relations' => [
                'bail',
                'array',
                new IsStringsArrayRule(),
                new AllowedRelationsRule($this->commentRepository),
                new IsUniqueArrayRule()
            ],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $defaultSortData = [
            [
                'sortField' => Comment::getDefaultSortField(),
                'sortDirection' => Comment::getDefaultSortDirection()
            ]
        ];
        $sortData = $request->input('sort', $defaultSortData);
        $relationData = $request->input('relations', []);
        $page = (int)$request->input('page', 1);
        $offset = $this->paginationHelper->getOffset($page);
        $sortData = $this->sortDataParser->parseSortData($sortData);
        $comments = $this->commentRepository->get($offset, $sortData, [], $relationData);

        return new JsonResponse($comments);
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
            'text' => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'post_id' => ['required', 'integer', 'exists:posts,id']
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $comment = $this->commentRepository->create([
            'text' => $request->input('text'),
            'user_id' => $request->input('user_id'),
            'post_id' => $request->input('post_id')
        ]);

        return new JsonResponse($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $comment = $this->commentRepository->find($id);

        if (is_null($comment)) {
            return new JsonResponse(['error' => 'Comment does not exist'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($comment);
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
        $comment = $this->commentRepository->find($id);

        if (is_null($comment)) {
            return new JsonResponse(['error' => 'Comment does not exist'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'text' => ['required', 'string'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'post_id' => ['required', 'integer', 'exists:posts,id']
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->commentRepository->update($id, [
            'text' => $request->input('text'),
            'user_id' => $request->input('user_id'),
            'post_id' => $request->input('post_id')
        ]);
        $comment->refresh();

        return new JsonResponse($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $comment = $this->commentRepository->find($id);

        if (is_null($comment)) {
            return new JsonResponse(['error' => 'Comment does not exist'], Response::HTTP_NOT_FOUND);
        }

        $this->commentRepository->delete($id);

        return new JsonResponse(['success' => true]);
    }
}

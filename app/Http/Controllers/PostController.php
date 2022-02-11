<?php

namespace App\Http\Controllers;

use App\Exceptions\FilterException;
use App\Factories\FilterCriteriaFactory;
use App\Rules\AllowedFilterableColumnsRule;
use App\Rules\AllowedRelationsRule;
use App\Rules\AllowedSortFieldsRule;
use App\Rules\ArrayKeyRule;
use App\Rules\FilterCriteriaKeyRule;
use App\Rules\IsArrayOfArraysRule;
use App\Rules\IsStringsArrayRule;
use App\Rules\IsUniqueArrayRule;
use App\Rules\SortDirectionAllowedRule;
use App\Services\SortDataParser;
use App\Repositories\PostRepository;
use App\Models\Post;
use App\Services\PaginationHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /** @var PostRepository */
    public PostRepository $postRepository;

    /** @var PaginationHelper */
    public PaginationHelper $paginationHelper;

    /** @var SortDataParser */
    public SortDataParser $sortDataParser;

    public function __construct(
        PostRepository $postRepository,
        PaginationHelper $paginationHelper,
        SortDataParser $sortDataParser
    )
    {
        $this->postRepository = $postRepository;
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
//        dd(http_build_query([
//            'relation' => [
//                [
//                    'user' => [123 , 'name'],
//                ]
//            ]
//        ]));
        $validator = Validator::make($request->all(), [
            'sort' => [
                'bail',
                'array',
                new IsArrayOfArraysRule(),
                new ArrayKeyRule(['sortDirection', 'sortField']),
                new SortDirectionAllowedRule(),
                new AllowedSortFieldsRule($this->postRepository)
            ],
            'filter' => [
                'bail',
                'array',
                new IsArrayOfArraysRule(),
                new ArrayKeyRule(['criteria', 'value']),
                new FilterCriteriaKeyRule(),
                new AllowedFilterableColumnsRule($this->postRepository)
            ],
            'relations' => [
                'bail',
                'array',
                new IsStringsArrayRule(),
                new AllowedRelationsRule($this->postRepository),
                new IsUniqueArrayRule()
            ],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $defaultSortData = [
            [
                'sortField' => Post::getDefaultSortField(),
                'sortDirection' => Post::getDefaultSortDirection()
            ]
        ];
        $sortData = $request->input('sort', $defaultSortData);
        $filterData = $request->input('filter', []);
        $relationData = $request->input('relations', []);
        $page = (int)$request->input('page', 1);
        $offset = $this->paginationHelper->getOffset($page);

        try {
            $filterCriterias = FilterCriteriaFactory::makeCriterias($filterData);
        } catch (FilterException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $sortData = $this->sortDataParser->parseSortData($sortData);
        $posts = $this->postRepository->get($offset, $sortData, $filterCriterias, $relationData);

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
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'relations' => [
                'array',
                new IsStringsArrayRule(),
                new AllowedRelationsRule($this->postRepository),
                new IsUniqueArrayRule()
            ]
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $relationData = $request->input('relations', []);
        $post = $this->postRepository->find($id, $relationData);

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

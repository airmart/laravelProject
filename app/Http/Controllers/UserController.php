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
use App\Services\RequestDataCleaner;
use App\Services\SortDataParser;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Services\PaginationHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var PaginationHelper */
    private PaginationHelper $paginationHelper;

    /** @var SortDataParser */
    private SortDataParser $sortDataParser;

    public function __construct(
        UserRepository $userRepository,
        PaginationHelper $paginationHelper,
        SortDataParser $sortDataParser,
        RequestDataCleaner $requestCleaner
    ) {
        $this->userRepository = $userRepository;
        $this->paginationHelper = $paginationHelper;
        $this->sortDataParser = $sortDataParser;
        $requestCleaner->clean($userRepository);
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
                new AllowedSortFieldsRule($this->userRepository)
            ],
            'filter' => [
                'bail',
                'array',
                new IsArrayOfArraysRule(),
                new ArrayKeyRule(['criteria', 'value']),
                new FilterCriteriaKeyRule(),
                new AllowedFilterableColumnsRule($this->userRepository)
            ],
            'relations' => [
                'bail',
                'array',
                new IsStringsArrayRule(),
                new AllowedRelationsRule($this->userRepository),
                new IsUniqueArrayRule()
            ],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $defaultSortData = [
            [
                'sortField' => User::getDefaultSortField(),
                'sortDirection' => User::getDefaultSortDirection()
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
        $users = $this->userRepository->get($offset, $sortData, $filterCriterias, $relationData);

        return new JsonResponse($users);
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
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', Password::default(), 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $this->userRepository->create([
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
            'email' => $request->input('email')
        ]);

        return new JsonResponse($user);
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
                'bail',
                'array',
                new IsStringsArrayRule(),
                new AllowedRelationsRule($this->userRepository),
                new IsUniqueArrayRule()
            ]
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $relationData = $request->input('relations', []);

        try {
            $this->relationValidator->validateRelationData($this->userRepository, $relationData);
        } catch (RelationException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $this->userRepository->find($id, $relationData);

        if (is_null($user)) {
            return new JsonResponse(['error' => 'User does not exist'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (is_null($user)) {
            return new JsonResponse(['error' => 'User does not exist'], Response::HTTP_NOT_FOUND);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', Password::default(), 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id]
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->userRepository->update($id, [
            'name' => $request->input('name'),
            'password' => Hash::make($request->input('password')),
            'email' => $request->input('email')
        ]);
        $user->refresh();

        return new JsonResponse($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (is_null($user)) {
            return new JsonResponse(['error' => 'User does not exist'], Response::HTTP_NOT_FOUND);
        }

        $this->userRepository->delete($id);

        return new JsonResponse(['success' => true]);
    }
}

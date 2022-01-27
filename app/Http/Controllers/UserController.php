<?php

namespace App\Http\Controllers;

use App\Exceptions\FilterException;
use App\Exceptions\SortingException;
use App\Factories\FilterCriteriaFactory;
use App\Services\FilterDataValidator;
use App\Services\SortDataParser;
use App\Services\SortDataValidator;
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
    public UserRepository $userRepository;

    /** @var PaginationHelper */
    public PaginationHelper $paginationHelper;

    /** @var SortDataValidator */
    public SortDataValidator $sortDataValidator;

    /** @var SortDataParser */
    public SortDataParser $sortDataParser;

    /** @var FilterDataValidator */
    public FilterDataValidator $filterValidator;

    public function __construct(
        UserRepository $userRepository,
        PaginationHelper $paginationHelper,
        SortDataValidator $sortDataValidator,
        SortDataParser $sortDataParser,
        FilterDataValidator $filterValidator
    ) {
        $this->userRepository = $userRepository;
        $this->paginationHelper = $paginationHelper;
        $this->sortDataValidator = $sortDataValidator;
        $this->sortDataParser = $sortDataParser;
        $this->filterValidator = $filterValidator;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $defaultSortData = [
            [
                'sortField' => User::getDefaultSortField(),
                'sortDirection' => User::getDefaultSortDirection()
            ]
        ];
        $sortData = (array)$request->input('sort', $defaultSortData);
        $filterData = (array)$request->input('filter', []);
        $page = (int)$request->input('page', 1);
        $offset = $this->paginationHelper->getOffset($page);

        try {
            $this->sortDataValidator->validateSortData($this->userRepository, $sortData);
        } catch (SortingException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $this->filterValidator->validateFilterData($this->userRepository, $filterData);
            $filterCriterias = FilterCriteriaFactory::makeCriterias($filterData);
        } catch (FilterException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $sortData = $this->sortDataParser->parseSortData($sortData);
        $users = $this->userRepository->get($offset, $sortData, $filterCriterias);

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
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

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

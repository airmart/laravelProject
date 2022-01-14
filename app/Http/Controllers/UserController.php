<?php

namespace App\Http\Controllers;

use App\Http\Repositories\UserRepository;
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

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $users = $this->userRepository->get();

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

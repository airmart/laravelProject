<?php

namespace App\Http\Controllers;

use App\Constants\RoleConstants;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /** @var UserRepository */
    private UserRepository $userRepository;

    /** @var RoleRepository  */
    private RoleRepository $roleRepository;

    public function __construct(UserRepository $userRepository, RoleRepository $roleRepository)
    {
        $this->userRepository = $userRepository;
        $this->roleRepository = $roleRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users'],
            'password' => ['required', 'string']
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $email = $request->post('email');
        $password = $request->post('password');
        $credentials = ['email' => $email, 'password' => $password];

        if (!Auth::attempt($credentials)) {
            return new JsonResponse(['errors' => 'Wrong credentials'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return new JsonResponse(Auth::user());
    }

    /**
     * @param Request $request
     * @param
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', Password::default(), 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(['errors' => $validator->messages()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $role = $this->roleRepository->queryBuilder->where('name', RoleConstants::USER_ROLE_NAME)->first();

        /** @var Authenticatable $user */
        $user = $this->userRepository->create([
            'name' => $request->post('name'),
            'password' => Hash::make($request->post('password')),
            'email' => $request->post('email'),
            'role_id' => $role->id
        ]);
        Auth::login($user);

        return new JsonResponse($user);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::logout();

        return new JsonResponse(['success' => 'true']);
    }

    /**
     * @return JsonResponse
     */
    public function current(): JsonResponse
    {
        return new JsonResponse(Auth::user());
    }
}

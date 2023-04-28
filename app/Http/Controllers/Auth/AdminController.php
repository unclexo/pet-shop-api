<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Requests\Auth\UserListingRequest;
use App\Models\User;
use App\Traits\NeedsCustomResponse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    use NeedsCustomResponse;

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $request->authenticate();

        $user->tokenize('Admin login');

        return $this->customJsonResponse(data: $user);
    }

    public function register(RegistrationRequest $request): JsonResponse
    {
        $user = auth()->user()->create(
            array_merge($request->validated(), ['is_admin' => 1])
        );

        $user->tokenize('Admin registration');

        return $this->customJsonResponse(data: $user);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->jwtToken()->delete();

        return $this->customJsonResponse();
    }

    public function listUsers(UserListingRequest $request): LengthAwarePaginator
    {
        $data = $request->getPaginationData();

        return User::query()
            ->nonAdminUsers()
            ->orderBy($data->sortBy, $data->desc)
            ->paginate($data->limit, '*', 'page', $data->page);
    }
}

<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Models\User;
use App\Traits\NeedsCustomResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use NeedsCustomResponse;

    public function login(LoginRequest $request): JsonResponse
    {
        $user = $request->authenticate();

        $user->tokenize('User login');

        return $this->customJsonResponse(data: $user);
    }

    public function register(RegistrationRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        $user->tokenize('User registration');

        return $this->customJsonResponse(data: $user);
    }

    public function edit(RegistrationRequest $request, User $uuid): JsonResponse
    {
        $this->authorize('update', $uuid);

        $uuid->update($request->validated());

        return $this->customJsonResponse(data: $uuid);
    }

    public function logout(): JsonResponse
    {
        auth()->user()?->jwtToken()?->delete();

       return $this->customJsonResponse();
    }
}

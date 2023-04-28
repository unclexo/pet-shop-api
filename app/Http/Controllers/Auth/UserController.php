<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Resources\ApiResource;
use App\Models\User;

class UserController extends Controller
{
    public function login(LoginRequest $request): ApiResource
    {
        $user = $request->authenticate();

        $user->tokenize('User login');

        return new ApiResource($user);
    }

    public function register(RegistrationRequest $request): ApiResource
    {
        $user = User::create($request->validated());

        $user->tokenize('User registration');

        return new ApiResource($user);
    }

    public function edit(RegistrationRequest $request, User $uuid): ApiResource
    {
        $this->authorize('update', $uuid);

        $uuid->update($request->validated());

        return new ApiResource($uuid);
    }

    public function logout(): ApiResource
    {
        auth()->user()?->jwtToken()?->delete();

        return new ApiResource([]);
    }
}

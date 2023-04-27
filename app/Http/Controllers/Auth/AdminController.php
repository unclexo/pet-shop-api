<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Resources\ApiResource;
use App\Models\User;

class AdminController extends Controller
{
    public function login(LoginRequest $request): ApiResource
    {
        $user = $request->authenticate();

        $user->tokenize('Admin login');

        return new ApiResource($user);
    }

    public function register(RegistrationRequest $request): ApiResource
    {
        $user = User::create(array_merge($request->validated(), ['is_admin' => 1]));

        $user->tokenize('Admin registration');

        return new ApiResource($user);
    }
}

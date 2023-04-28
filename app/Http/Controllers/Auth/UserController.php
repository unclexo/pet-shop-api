<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Http\Resources\ApiResource;
use App\Models\User;

class UserController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = $request->authenticate();

        $user->tokenize('User login');

        return new ApiResource($user);
    }

    public function register(RegistrationRequest $request)
    {
        $user = User::create($request->validated());

        $user->tokenize('User registration');

        return new ApiResource($user);
    }
}

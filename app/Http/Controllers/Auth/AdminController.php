<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        return response()->json([
            'data' => [
                'token' => app('jwt')->token()->toString()
            ]
        ], 200);
    }

    public function register(RegistrationRequest $request): JsonResponse
    {
        User::create(array_merge($request->validated(), ['is_admin' => 1]));

        return response()->json([
            'data' => [
                'token' => app('jwt')->token()->toString()
            ]
        ], 200);
    }
}

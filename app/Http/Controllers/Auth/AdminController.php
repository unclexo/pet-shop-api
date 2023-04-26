<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;

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
}

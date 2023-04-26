<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function login(LoginRequest $request)
    {
        $email = $request->input('email');

        if (! $user = User::where('email', $email)->first()) {
            throw ValidationException::withMessages([
                'email' => __("auth.failed")
            ]);
        }

        if (! Hash::check(
            $password = $request->input('password'),
            $user->password)
        ) {
            throw ValidationException::withMessages([
                'email' => __("auth.failed")
            ]);
        }

        if (Hash::needsRehash($user->password)) {
            $user->password = bcrypt($password);
            $user->save();
        }

        return response()->json([
            'data' => [
                'token' => app('jwt')->token()->toString()
            ]
        ], 200);
    }
}

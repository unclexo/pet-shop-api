<?php

namespace App\Http\Requests\Auth;


use App\Models\User;
use App\Traits\NeedsCustomResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;

class LoginRequest extends FormRequest
{
    use NeedsCustomResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function authenticate(): User
    {
        $email = $this->input('email');

        if (! $user = User::where('email', $email)->first()) {
            $this->throwHttpResponseException(
                errors: ['email' => __('auth.failed')],
            );
        }

        if (! Hash::check(
            $password = $this->input('password'),
            $user->password)
        ) {
            $this->throwHttpResponseException(
                errors: ['email' => __('auth.failed')],
            );
        }

        if (Hash::needsRehash($user->password)) {
            $user->password = bcrypt($password);
            $user->save();
        }

        return $user;
    }
}

<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrationRequest extends FormRequest
{
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
            'first_name' => ['required', 'string', 'max:30'],
            'last_name' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
            'avatar' => ['string', 'uuid', Rule::requiredIf(fn () => (bool) $this->user()?->isAdmin())],
            'address' => ['required', 'string'],
            'phone_number' => ['required', 'string', 'max:20'],
            'is_marketing' => ['nullable', 'boolean'],
        ];
    }
}

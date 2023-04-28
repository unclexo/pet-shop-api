<?php

namespace App\Http\Requests\Auth;


use App\Rules\ColumnExists;
use App\Traits\NeedsCustomResponse;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;

class UserListingRequest extends FormRequest
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
            'page' => ['sometimes', 'integer'],
            'limit' => ['sometimes', 'integer'],
            'sortBy' => [
                'sometimes',
                'string',
                function (string $attribute, mixed $value, Closure $fail) {
                    if (! Schema::hasColumn('users', $value)) {
                        $this->throwHttpResponseException(errors: [
                            'sortBy' => "The {$value} is an invalid column."]
                        );
                    }
                },
            ],
            'desc' => ['sometimes', 'boolean'],
        ];
    }

    public function getPaginationData(): object
    {
        $data = $this->validated();

        return (object) [
            'page' => $data['page'] ?? null,
            'limit' => $data['limit'] ?? null,
            'sortBy' => $data['sortBy'] ?? 'first_name',
            'desc' => isset($data['desc'])
                ? $data['desc'] == true ? 'desc' : 'asc'
                : 'asc',
        ];
    }
}

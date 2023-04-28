<?php


namespace App\Traits;


use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait NeedsCustomResponse
{
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException($this->customJsonResponse(
            statusCode: JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
            errors: $validator->errors()
        ));
    }

    protected function throwHttpResponseException(
        int $statusCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY,
        mixed $data = [],
        string $error = null,
        mixed $errors = []
    ): void {
        throw new HttpResponseException($this->customJsonResponse(
            $statusCode,
            $data,
            $error,
            $errors,
        ));
    }

    protected function customJsonResponse(
        int $statusCode = Response::HTTP_OK,
        mixed $data = [],
        string $error = null,
        mixed $errors = []
    ): JsonResponse {
        return response()->json([
            'success' => $statusCode >= 200 && $statusCode <= 299 ? 1 : 0,
            'data' => $data,
            'error' => $error,
            'errors' => $errors,
        ], $statusCode);
    }
}

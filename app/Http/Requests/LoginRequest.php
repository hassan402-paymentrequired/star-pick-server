<?php

namespace App\Http\Requests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !preg_match('/^\d{10,15}$/', $value)) {
                        $fail('The ' . $attribute . ' must be a valid email address or phone number.');
                    }
                },
            ],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ];
    }

    protected function failedAuthorization()
    {
        throw new AuthorizationException('You are not allowed to perform this action.');
    }
}

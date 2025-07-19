<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePeerRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:225'],
            'amount' => ['required', 'numeric', 'min:100'],
            'private' => ['required', 'boolean'],
            'limit' => ['required', 'numeric', 'min:1'],
            'ratio' => ['required', 'numeric', 'in:1,2'],
        ];
    }
}

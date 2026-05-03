<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required_without_all:documento_tipo_id,documento_numero',
                'email'
            ],
            'documento_tipo_id' => [
                'required_without:email',
                'integer',
                Rule::exists('documento_tipos', 'id')
            ],
            'documento_numero' => [
                'required_without:email',
                'numeric',
                'digits_between:7,15'
            ],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'documento_numero.numeric' => 'El número de documento debe contener solo números.',
            'documento_numero.digits_between' => 'El número de documento debe tener entre 7 y 15 dígitos.',
        ];
    }
}

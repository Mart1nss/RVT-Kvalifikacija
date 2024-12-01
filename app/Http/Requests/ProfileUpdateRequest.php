<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:10',
                'regex:/^[a-zA-Z0-9]+$/',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique(User::class)->ignore($this->user()->id)
            ],
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.min' => 'NAME MUST BE AT LEAST 3 CHARACTERS',
            'name.max' => 'NAME CANNOT BE MORE THAN 10 CHARACTERS',
            'name.regex' => 'NAME CAN ONLY CONTAIN LETTERS AND NUMBERS (NO SPACES OR SYMBOLS)',
            'email.email' => 'PLEASE ENTER A VALID EMAIL ADDRESS',
            'email.regex' => 'PLEASE ENTER A VALID EMAIL WITH PROPER DOMAIN',
            'email.unique' => 'THIS EMAIL IS ALREADY TAKEN',
        ];
    }
}

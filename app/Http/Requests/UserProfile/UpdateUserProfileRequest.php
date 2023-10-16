<?php

namespace App\Http\Requests\UserProfile;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserProfileRequest extends FormRequest
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
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore(Auth::user()->id)],
            'name' => 'required|string|max:255',
            'phones' => ['required', 'numeric', 'digits:12', 'regex:/^255\d{9}$/', Rule::unique('users', 'phone')->ignore(Auth::user()->id)],
        ];
    }
}
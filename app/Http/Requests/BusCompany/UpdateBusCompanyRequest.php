<?php

namespace App\Http\Requests\BusCompany;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusCompanyRequest extends FormRequest
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
            'email' => ['required', 'email', Rule::unique('bus_companies', 'email')->ignore(Auth::user()->bus_company_id)],
            // 'email' => ['required', 'email', Rule::unique('bus_companies', 'email')->ignore('edcb835f-892e-462b-b788-674b09825ff98')],
            'name' => 'required|string|max:255',
            'phone_one' => ['required', 'numeric', 'digits:12', 'regex:/^255\d{9}$/', Rule::unique('bus_companies', 'phone_one')->ignore(Auth::user()->bus_company_id)],
            'phone_two' => ['nullable', 'numeric', 'digits:12', 'regex:/^255\d{9}$/', Rule::unique('bus_companies', 'phone_two')->ignore(Auth::user()->bus_company_id)],
            'logo' => 'required|string',
            'description' => 'nullable|string',
            'policy' => 'nullable|string',
            'business_licence' => 'required|string',
            'status' => 'boolean',
        ];
    }
}
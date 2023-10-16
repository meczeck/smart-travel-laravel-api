<?php

namespace App\Http\Requests\BusCompany;

use Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateBusCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**d
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:bus_companies,email',
            'name' => 'required|string|max:255',
            'phone_one' => 'required|numeric|digits:12|regex:/^255\d{9}$/|unique:bus_companies,phone_one',
            'phone_two' => 'nullable|numeric|digits:12|regex:/^255\d{9}$/|unique:bus_companies,phone_two',
            'logo' => 'required|string',
            'description' => 'nullable|string',
            'policy' => 'nullable|string',
            'business_licence' => 'required|string',
            'status' => 'boolean',
        ];


    }

    public function errors()
    {
        return [
            'phone_one.regex:/^255\d{9}$/' => 'Phone Number must include the country code 255'
        ];
    }
}
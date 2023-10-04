<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GetUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->resource['user']['id'],
                'email' => $this->resource['user']['email'],
                'phone' => $this->resource['user']['phone'],
                'name' => $this->resource['user']['name'],
                'otp_verification' => $this->resource['user']['otp_verification'],
                'registration_verification' => $this->resource['user']['registration_verification'],
                'status' => $this->resource['user']['status'],

            ],
            'permissions' => PermissionsReource::collection($this->resource['permissions'])
        ];
    }
}

class PermissionsReource extends JsonResource
{
    public function toArray(Request $request)
    {
        return $this->name;

    }
}
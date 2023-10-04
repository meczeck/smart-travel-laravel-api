<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAuthenticationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'user' => [
                'id' => $this->resource['user']['id'],
                'email' => $this->resource['user']['email'],
                'phone' => $this->resource['user']['phone'],
                'name' => $this->resource['user']['name'],
                'otp_verification' => $this->resource['user']['otp_verification'],
                'registration_verification' => $this->resource['user']['registration_verification'],
                'status' => $this->resource['user']['status'],
                // 'updated_at' => $this->resource['user']['updated_at'],
                // 'created_at' => $this->resource['user']['created_at'],
            ],
            'token' => $this->resource['token'] ?? '',
            'otp_expires_at' => $this->resource['otp_expires_at'] ?? '',
            'otp' => $this->resource['otp'] ?? '',
            // 'permissions' => PermissionResource::collection($this->resource['permissions']->pluck('name')),
            'permissions' => $this->resource['permissions']->pluck('name')
        ];
    }
}


class PermissionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'id' => $this->id,
            'name' => $this->name
        ];
    }
}
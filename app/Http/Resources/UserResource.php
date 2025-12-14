<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource for User model.
 * 
 * Transforms User model data for API responses,
 * ensuring consistent output format and excluding sensitive data.
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'detail' => $this->when($this->relationLoaded('detail') && $this->detail, [
                'phone' => $this->detail?->phone,
                'address' => $this->detail?->address,
                'city' => $this->detail?->city,
                'state' => $this->detail?->state,
                'postal_code' => $this->detail?->postal_code,
                'country' => $this->detail?->country,
                'date_of_birth' => $this->detail?->date_of_birth?->toDateString(),
            ]),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}


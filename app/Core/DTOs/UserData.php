<?php

declare(strict_types=1);

namespace App\Core\DTOs;

use Illuminate\Http\Request;

/**
 * Data Transfer Object for User data.
 * 
 * Provides a strictly typed, immutable structure for transferring
 * user-related data between application layers.
 */
readonly class UserData
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $password = null,
        public ?UserDetailData $detail = null,
    ) {
    }

    /**
     * Create a UserData instance from an HTTP request.
     */
    public static function fromRequest(Request $request): self
    {
        // Check if any detail fields are present
        $hasDetailFields = $request->hasAny([
            'phone',
            'address',
            'city',
            'state',
            'postal_code',
            'country',
            'date_of_birth'
        ]);

        return new self(
            name: $request->input('name'),
            email: $request->input('email'),
            password: $request->input('password'),
            detail: $hasDetailFields ? UserDetailData::fromRequest($request) : null,
        );
    }

    /**
     * Create a UserData instance from an array.
     */
    public static function fromArray(array $data): self
    {
        $detail = isset($data['detail']) ? UserDetailData::fromArray($data['detail']) : null;

        return new self(
            name: $data['name'],
            email: $data['email'],
            password: $data['password'] ?? null,
            detail: $detail,
        );
    }

    /**
     * Convert the DTO to an array, excluding null values.
     */
    public function toArray(): array
    {
        $data = array_filter([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ], fn($value) => $value !== null);

        if ($this->detail !== null) {
            $data['detail'] = $this->detail->toArray();
        }

        return $data;
    }
}


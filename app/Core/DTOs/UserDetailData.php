<?php

declare(strict_types=1);

namespace App\Core\DTOs;

use Illuminate\Http\Request;

/**
 * Data Transfer Object for UserDetail data.
 * 
 * Provides a strictly typed, immutable structure for transferring
 * user detail information between application layers.
 */
readonly class UserDetailData
{
    public function __construct(
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $state = null,
        public ?string $postalCode = null,
        public ?string $country = null,
        public ?string $dateOfBirth = null,
    ) {
    }

    /**
     * Create a UserDetailData instance from an HTTP request.
     * Converts empty strings to null for consistent handling.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            phone: self::nullIfEmpty($request->input('phone')),
            address: self::nullIfEmpty($request->input('address')),
            city: self::nullIfEmpty($request->input('city')),
            state: self::nullIfEmpty($request->input('state')),
            postalCode: self::nullIfEmpty($request->input('postal_code')),
            country: self::nullIfEmpty($request->input('country')),
            dateOfBirth: self::nullIfEmpty($request->input('date_of_birth')),
        );
    }

    /**
     * Create a UserDetailData instance from an array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            phone: self::nullIfEmpty($data['phone'] ?? null),
            address: self::nullIfEmpty($data['address'] ?? null),
            city: self::nullIfEmpty($data['city'] ?? null),
            state: self::nullIfEmpty($data['state'] ?? null),
            postalCode: self::nullIfEmpty($data['postal_code'] ?? null),
            country: self::nullIfEmpty($data['country'] ?? null),
            dateOfBirth: self::nullIfEmpty($data['date_of_birth'] ?? null),
        );
    }

    /**
     * Convert the DTO to an array for database storage.
     * Only includes non-null and non-empty values.
     */
    public function toArray(): array
    {
        return array_filter([
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
            'date_of_birth' => $this->dateOfBirth,
        ], fn($value) => $value !== null && $value !== '');
    }

    /**
     * Check if any detail data is present.
     */
    public function hasData(): bool
    {
        return $this->isNotEmpty($this->phone)
            || $this->isNotEmpty($this->address)
            || $this->isNotEmpty($this->city)
            || $this->isNotEmpty($this->state)
            || $this->isNotEmpty($this->postalCode)
            || $this->isNotEmpty($this->country)
            || $this->isNotEmpty($this->dateOfBirth);
    }

    /**
     * Convert empty string to null.
     */
    private static function nullIfEmpty(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        return $value;
    }

    /**
     * Check if a value is not empty.
     */
    private function isNotEmpty(?string $value): bool
    {
        return $value !== null && $value !== '';
    }
}


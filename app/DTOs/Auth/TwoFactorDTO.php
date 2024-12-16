<?php

namespace App\DTOs\Auth;

use App\Enums\TwoFactorType;

readonly class TwoFactorDTO
{
    public function __construct(
        public int $userId,
        public string $code,
        public string $type
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            code: $data['code'],
            type: $data['type']
        );
    }
}

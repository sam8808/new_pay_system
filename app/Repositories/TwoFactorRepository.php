<?php

namespace App\Repositories;

use App\Models\TwoFactorAuth;
use App\DTOs\Auth\TwoFactorDTO;
use Carbon\Carbon;

class TwoFactorRepository
{
    // Создание нового кода двухфакторной аутентификации
    public function create(TwoFactorDTO $dto): TwoFactorAuth
    {
        return TwoFactorAuth::create([
            'user_id' => $dto->userId,
            'code' => $dto->code,
            'type' => $dto->type,
            'expires_at' => Carbon::now()->addMinutes(10)
        ]);
    }

    // Поиск активного кода для проверки
    public function findValidCode(int $userId, string $code, string $type): ?TwoFactorAuth
    {
        return TwoFactorAuth::where('user_id', $userId)
            ->where('code', $code)
            ->where('type', $type)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();
    }

    // Отметить код как использованный
    public function markAsUsed(TwoFactorAuth $twoFactorAuth): bool
    {
        return $twoFactorAuth->update(['is_used' => true]);
    }

    // Удаление старых кодов для пользователя
    public function deleteOldCodes(int $userId): bool
    {
        return TwoFactorAuth::where('user_id', $userId)
            ->where('expires_at', '<', Carbon::now())
            ->delete();
    }
}

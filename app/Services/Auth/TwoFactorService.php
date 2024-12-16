<?php


namespace App\Services\Auth;

use App\Models\User;
use App\DTOs\Auth\TwoFactorDTO;
use App\Repositories\TwoFactorRepository;
use App\Enums\TwoFactorType;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCode;

class TwoFactorService
{
    const EMAIL = 'email';
    const SMS = 'sms';

    public function __construct(
        private readonly TwoFactorRepository $twoFactorRepository
    ) {}

    public function sendCode(User $user): bool
    {
        // Очищаем старые коды
        $this->twoFactorRepository->deleteOldCodes($user->id);

        // Генерируем новый код
        $code = $this->generateCode();

        // Создаем запись в базе данных
        $this->twoFactorRepository->create(new TwoFactorDTO(
            userId: $user->id,
            code: $code,
            type: $user->two_factor_type
        ));

         // Отправляем код в зависимости от типа 2FA
         return match ($user->two_factor_type) {
            self::EMAIL => $this->sendEmailCode($user, $code),
            self::SMS => $this->sendSmsCode($user, $code),
         };
    }

    public function verify(User $user, string $code): bool
    {
        $validCode = $this->twoFactorRepository->findValidCode(
            $user->id,
            $code,
            $user->two_factor_type
        );

        if (!$validCode) {
            return false;
        }

        return $this->twoFactorRepository->markAsUsed($validCode);
    }
    public function toggle(User $user, string $type): bool
    {
        // Если двухфакторная аутентификация уже включена с таким же типом, выключаем её
        if ($user->two_factor_enabled && $user->two_factor_type?->value === $type) {
            return $this->disable($user);
        }

        // Включаем двухфакторную аутентификацию с новым типом
        return $this->enable($user, TwoFactorType::from($type));
    }

    // Приватный метод для включения 2FA
    private function enable(User $user, TwoFactorType $type): bool
    {
        // Обновляем настройки пользователя
        $user->update([
            'two_factor_enabled' => true,
            'two_factor_type' => $type
        ]);

        // Если успешно включили, возвращаем true
        return true;
    }

    // Приватный метод для выключения 2FA
    private function disable(User $user): bool
    {
        // Обновляем настройки пользователя
        $user->update([
            'two_factor_enabled' => false,
            'two_factor_type' => null
        ]);

        // Очищаем все старые коды
        $this->twoFactorRepository->deleteOldCodes($user->id);

        // Если успешно выключили, возвращаем false
        return false;
    }

    private function generateCode(): string
    {
        return str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

   private function sendEmailCode(User $user, string $code): bool
   {
       try {
           Mail::to($user->email)->send(new TwoFactorCode($code));
           return true; // Success
       } catch (\Exception $e) {
           return false; // Failure
       }
   }

    private function sendSmsCode(User $user, string $code): bool
    {
        // Здесь будет интеграция с SMS-сервисом
        // Пример: $this->smsService->send($user->phone, $code);
    }
}

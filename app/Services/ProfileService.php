<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    private User $user;
    private array $data;
    private array $errors = [];
    private bool $fail = false;

    private const VALIDATION_RULES = [
        'email' => ['sometimes', 'email', 'max:255'],
        'telegram' => ['nullable', 'string', 'max:255'],
        'phone' => ['nullable', 'string', 'max:255'],
        'settings' => ['sometimes', 'json'],
    ];

    public function __construct(array $data, User $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    public static function init(array $data, User $user): self
    {
        return new self($data, $user);
    }

    public function updateProfile(): bool
    {
        try {
            if (!$this->validateProfile()) {
                return false;
            }

            DB::transaction(function () {
                if ($this->user->isDirty('email')) {
                    $this->user->email_verified_at = null;
                    $this->user->is_verified = false;
                }

                $this->user->save();
            });

            return true;
        } catch (Exception $e) {
            $this->handleError($e);
            return false;
        }
    }

    public function updateContacts(): bool
    {
        try {
            if (!$this->validateContacts()) {
                return false;
            }

            $this->user->fill([
                'telegram' => $this->data['telegram'] ?? null,
                'phone' => $this->data['phone'] ?? null,
            ])->save();

            return true;
        } catch (Exception $e) {
            $this->handleError($e);
            return false;
        }
    }

    public function deleteAccount(): bool
    {
        try {
            if (!Hash::check($this->data['password'], $this->user->password)) {
                $this->errors['password'] = [__('auth.password')];
                return false;
            }

            $this->user->delete();
            return true;
        } catch (Exception $e) {
            $this->handleError($e);
            return false;
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function fail(): bool
    {
        return $this->fail;
    }

    private function validateProfile(): bool
    {
        $validator = validator($this->data, [
            'email' => self::VALIDATION_RULES['email'],
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();
            return false;
        }

        return true;
    }

    private function validateContacts(): bool
    {
        $validator = validator($this->data, [
            'telegram' => self::VALIDATION_RULES['telegram'],
            'phone' => self::VALIDATION_RULES['phone'],
        ]);

        if ($validator->fails()) {
            $this->errors = $validator->errors()->toArray();
            return false;
        }

        return true;
    }

    private function handleError(Exception $e): void
    {
        $this->fail = true;
        Log::error('Profile update error: ' . $e->getMessage(), [
            'user_id' => $this->user->id,
            'trace' => $e->getTraceAsString()
        ]);
        $this->errors = [__('Update error')];
    }
}

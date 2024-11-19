<?php

namespace App\Services\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;


class RegisterService
{
    private const IDENTIFY_LENGTH = 8;
    private const CHARACTERS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    private const VALIDATION_RULES = [
        'email' => ['required', 'email', 'max:255'],
        'password' => [
            'required',
            'confirmed',
            'min:8',
            'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
        ],
        'password_confirmation' => ['required'],
    ];

    private string $email;
    private string $password;
    private string $password_confirmation;
    private bool $agreement;
    private array $errors = [];
    private bool $fail = false;
    private ?User $user = null;

    public function __construct(array $data)
    {
        $this->email = trim($data['email'] ?? '');
        $this->password = $data['password'] ?? '';
        $this->password_confirmation = $data['password_confirmation'] ?? '';
        $this->agreement = (bool)($data['agreement'] ?? false);
    }

    public static function init(array $data): self
    {
        return new self($data);
    }


    public function validate(): bool
    {
        try {
            $validator = Validator::make(
                $this->getValidationData(),
                self::VALIDATION_RULES,
                $this->getValidationMessages()
            );

            if ($validator->fails()) {
                $this->errors = $validator->errors()->toArray();
                return false;
            }

            $this->validateBusinessRules();

            return true;
        } catch (Exception $e) {
            Log::error('Validation error: ' . $e->getMessage(), [
                'email' => $this->email,
                'trace' => $e->getTraceAsString()
            ]);
            $this->errors = [$e->getMessage()];
            return false;
        }
    }


    public function store(): void
    {
        try {
            DB::transaction(function () {
                $this->user = User::create([
                    'identify' => $this->generateIdentify(),
                    'email' => $this->email,
                    'password' => $this->hashPassword(),
                    'referrer_id' => $this->getReferrer(),
                ]);
            });
        } catch (Exception $e) {
            $this->handleStoreError($e);
            throw $e;
        }
    }

    public function user(): ?User
    {
        return $this->user;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function fail(): bool
    {
        return $this->fail;
    }

    private function validateBusinessRules(): void
    {
        if ($this->emailExists()) {
            throw new Exception(__('User with this email already exists'));
        }

        if (!$this->checkAgreement()) {
            throw new Exception(__('Terms agreement is required'));
        }
    }

    private function getValidationData(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
        ];
    }

    private function getValidationMessages(): array
    {
        return [
            'email.required' => __('Email is required'),
            'email.email' => __('Invalid email format'),
            'password.required' => __('Password is required'),
            'password.confirmed' => __('Passwords do not match'),
            'password.min' => __('Password must be at least 8 characters'),
            'password.regex' => __('Password must contain uppercase, lowercase and numbers'),
        ];
    }

    private function generateIdentify(string $prefix = ''): string
    {
        try {
            $result = bin2hex(random_bytes(self::IDENTIFY_LENGTH));
            return $prefix . substr($result, 0, self::IDENTIFY_LENGTH);
        } catch (Exception $e) {
            Log::warning('Fallback to less secure identify generation');
            return $this->generateIdentifyFallback($prefix);
        }
    }

    private function generateIdentifyFallback(string $prefix = ''): string
    {
        $characters = self::CHARACTERS;
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < self::IDENTIFY_LENGTH; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $prefix . $randomString;
    }

    private function emailExists(): bool
    {
        return User::where('email', $this->email)->exists();
    }

    private function getReferrer(): ?int
    {
        return Session::has('ref_code')
            ? User::where('ref_code', Session::get('ref_code'))->value('id')
            : null;
    }

    private function checkAgreement(): bool
    {
        return $this->agreement === true;
    }

    private function hashPassword(): string
    {
        return Hash::make($this->password);
    }

    private function handleStoreError(Exception $e): void
    {
        $this->fail = true;
        $errorMessage = __('Error while creating user: ') . $e->getMessage();
        Log::error($errorMessage, [
            'email' => $this->email,
            'trace' => $e->getTraceAsString()
        ]);
        $this->errors = [__('Registration error')];
    }
}

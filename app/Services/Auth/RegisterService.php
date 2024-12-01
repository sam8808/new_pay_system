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
        'telegram' => ['nullable', 'string', 'max:50'],
        'phone' => ['nullable', 'string', 'max:20'],
    ];


    private string $email;
    private string $account;
    private string $password;
    private string $password_confirmation;
    private ?string $telegram;
    private ?string $phone;
    private bool $agreement;
    private array $errors = [];
    private bool $fail = false;
    private ?User $user = null;


    public function __construct(array $data)
    {
        $this->email = trim(strtolower($data['email'] ?? ''));
        $this->account = $this->generateAccount('F');
        $this->password = $data['password'] ?? '';
        $this->password_confirmation = $data['password_confirmation'] ?? '';
        $this->telegram = !empty($data['telegram']) ? trim($data['telegram']) : null;
        $this->phone = !empty($data['phone']) ? trim($data['phone']) : null;
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
                    'account' => $this->account,
                    'email' => $this->email,
                    'password' => $this->hashPassword(),
                    'telegram' => $this->telegram,
                    'phone' => $this->phone,
                    'referrer_id' => $this->getReferrer(),
                    'is_active' => true,
                    'is_verified' => false
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
            'account.required' => __('Account name is required'),
            'account.unique' => __('This account name is already taken'),
            'password.required' => __('Password is required'),
            'password.confirmed' => __('Passwords do not match'),
            'password.min' => __('Password must be at least 8 characters'),
            'password.regex' => __('Password must contain uppercase, lowercase and numbers'),
        ];
    }


    private function generateAccount(string $prefix = ''): string
    {
        try {
            $result = bin2hex(random_bytes(self::IDENTIFY_LENGTH));
            return $prefix . substr($result, 0, self::IDENTIFY_LENGTH);
        } catch (Exception $e) {
            Log::warning('Fallback to less secure identify generation');
            return $this->generateAccountFallback($prefix);
        }
    }

    private function generateAccountFallback(string $prefix = ''): string
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

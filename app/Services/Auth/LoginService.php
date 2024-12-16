<?php

namespace App\Services\Auth;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;


class LoginService
{
    private const MAX_LOGIN_ATTEMPTS = 100;
    private const BLOCK_DURATION = 15;
    private const CACHE_KEY_PREFIX = 'login_attempts_';

    protected const RULES = [
        'email' => ['required', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:8'],
    ];

    protected const MESSAGES = [
        'email.required' => 'Email is required',
        'email.email' => 'Invalid email format',
        'email.max' => 'Email cannot be longer than :max characters',
        'password.required' => 'Password is required',
        'password.string' => 'Invalid password format',
        'password.min' => 'Password must be at least :min characters',
    ];


    protected string $email = '';
    protected string $password = '';
    protected array $errors = [];
    protected bool $fail = false;
    protected ?User $user = null;


    public function __construct(array $data)
    {
        $this->email = trim(strtolower($data['email'] ?? ''));
        $this->password = $data['password'] ?? '';
    }


    public static function init(array $data): static
    {
        return new static($data);
    }


    public function validate(): bool
    {
        try {
            if ($this->isBlocked()) {
                $this->errors = [__('Too many login attempts. Please try again later.')];
                return false;
            }

            $validator = Validator::make(
                $this->getCredentials(),
                static::RULES,
                static::MESSAGES
            );

            if ($validator->fails()) {
                $this->errors = $validator->errors()->toArray();
                return false;
            }

            if ($this->userExists()) {
                $this->resetLoginAttempts();
                return true;
            }

            $this->incrementLoginAttempts();
            $this->errors = [__('Invalid email or password')];

            return false;
        } catch (Exception $e) {
            $this->handleError($e, 'Validation error');
            return false;
        }
    }


    public function store(): void
    {
        try {
            $credentials = $this->getCredentials();

            if (!Auth::attempt($credentials, true)) {
                $this->fail = true;
                $this->errors = [__('Authentication error')];

                Log::warning('Failed login attempt', [
                    'email' => $this->email,
                    'ip' => request()->ip()
                ]);
                return;
            }
            $this->user = Auth::user();
            $this->updateLastLogin();

            Log::info('Successful login', [
                'user_id' => $this->user->id,
                'email' => $this->email
            ]);
        } catch (Exception $e) {
            $this->handleError($e, 'Authentication error');
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


    public function getUser(): ?User
    {
        return $this->user;
    }


    private function userExists(): bool
    {
        $this->user = User::where('email', $this->email)
            ->select(['id', 'email', 'password', 'is_active', 'is_verified'])
            ->first();

        if (!$this->user) {
            return false;
        }

        if (!$this->user->is_active) {
            $this->errors = [__('Account is deactivated')];
            return false;
        }

        if (!$this->user->is_verified) {
            Log::warning('Unverified user login attempt', [
                'user_id' => $this->user->id,
                'email' => $this->email
            ]);
        }

        return Hash::check($this->password, $this->user->password);
    }


    private function getCredentials(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password
        ];
    }


    private function handleError(Exception $e, string $context): void
    {
        $this->fail = true;
        Log::error("$context: " . $e->getMessage(), [
            'email' => $this->email,
            'trace' => $e->getTraceAsString()
        ]);
        $this->errors = [__('An error occurred. Please try again later.')];
    }


    private function updateLastLogin(): void
    {
        if ($this->user) {
            $this->user->update([
                'last_login_at' => now(),
            ]);
        }
    }


    private function isBlocked(): bool
    {
        $attempts = $this->getLoginAttempts();
        return $attempts >= self::MAX_LOGIN_ATTEMPTS;
    }


    private function getLoginAttempts(): int
    {
        return Cache::get($this->getAttemptsKey(), 0);
    }


    private function incrementLoginAttempts(): void
    {
        $attempts = $this->getLoginAttempts() + 1;
        Cache::put(
            $this->getAttemptsKey(),
            $attempts,
            now()->addMinutes(self::BLOCK_DURATION)
        );
    }


    private function resetLoginAttempts(): void
    {
        Cache::forget($this->getAttemptsKey());
    }


    private function getAttemptsKey(): string
    {
        return self::CACHE_KEY_PREFIX . request()->ip();
    }


}

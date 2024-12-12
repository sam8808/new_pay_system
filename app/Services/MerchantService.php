<?php

namespace App\Services;

use Exception;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class MerchantService
{
    private const ID_LENGTH = 9;
    private const KEY_LENGTH = 32;
    private const HASH_ALGORITHM = 'sha256';
    private const KEY_CHARACTERS = '0123456789#&%?:.abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';


    private const VALIDATION_RULES = [
        'title' => ['required', 'string', 'max:255'],
        'domain' => ['nullable', 'string', 'url'],
        'webhook_url' => ['required', 'string', 'unique:merchants,webhook_url', 'url:https'],
        'allowed_ips' => ['nullable', 'json'],
    ];



    private array $validatedData = [];



    public function __construct(
        private readonly Request $request
    ) {}


    public function validate(): self
    {
        $this->validatedData = $this->request->validate(array_merge(
            self::VALIDATION_RULES,
            $this->getUrlValidationRules()
        ));

        return $this;
    }


    public function create(): Merchant
    {
        try {
            return Merchant::create([
                'user_id' => $this->request->user()->id,
                'title' => $this->validatedData['title'],
                'domain' => $this->validatedData['domain'] ?? null,
                'merchant_id' => $this->generateId(),
                'api_key' => $this->generateKey(),
                'secret_key' => $this->generateKey(),
                'webhook_url' => $this->validatedData['webhook_url'],
                'allowed_ips' => $this->validatedData['allowed_ips'] ?? null,
                'is_active' => true,
            ]);

        } catch (Exception $e) {
            Log::error('Failed to create merchant', [
                'error' => $e->getMessage(),
                'user_id' => $this->request->user()->id,
                'data' => $this->validatedData
            ]);
            throw new Exception(__('Failed to create merchant'), 0, $e);
        }
    }


    public function getHash(Merchant $merchant): string
    {
        $requiredFields = ['order', 'amount', 'currency'];

        foreach ($requiredFields as $field) {
            if (!$this->request->has($field)) {
                throw new Exception(__("Missing required field: :field", ['field' => $field]));
            }
        }

        $data = [
            $merchant->m_id,
            $this->request->post('order'),
            $this->request->post('amount'),
            $this->request->post('currency'),
            $merchant->secret_key,
        ];

        return $this->generateHash($data);
    }


    private function getUrlValidationRules(): array
    {
        return [
            'success_url' => [
                function ($attribute, $value, $fail) {
                    $this->validateUrlStartsWith($attribute, $value, $fail);
                },
            ],
            'fail_url' => [
                function ($attribute, $value, $fail) {
                    $this->validateUrlStartsWith($attribute, $value, $fail);
                },
            ],
        ];
    }


    private function validateUrlStartsWith(string $attribute, string $value, callable $fail): void
    {
        $baseUrl = $this->request->input('webhook_url');
        if (!Str::startsWith($value, $baseUrl)) {
            $fail(__(':Attribute must start with base URL', [
                'attribute' => Str::title(str_replace('_', ' ', $attribute))
            ]));
        }
    }


    private function generateId(): int
    {
        try {
            $uniqueNumber = now()->timestamp * 1000 + random_int(1000, 9999);
            return (int) substr((string) $uniqueNumber, 0, self::ID_LENGTH);
        } catch (Exception $e) {
            Log::warning('Failed to generate merchant ID using random_int, falling back to mt_rand');
            return $this->generateIdFallback();
        }
    }


    private function generateIdFallback(): int
    {
        $baseValue = time() * 1000;
        $randomDigits = mt_rand(1000, 9999);
        $uniqueId = substr((string) ($baseValue + $randomDigits), 0, self::ID_LENGTH);

        return (int) $uniqueId;
    }


    private function generateKey(): string
    {
        try {
            return bin2hex(random_bytes(self::KEY_LENGTH / 2));
        } catch (Exception $e) {
            Log::warning('Failed to generate merchant key using random_bytes, falling back to Str::random');
            return $this->generateKeyFallback();
        }
    }


    private function generateKeyFallback(): string
    {
        $characters = self::KEY_CHARACTERS;
        $charactersLength = strlen($characters);
        $key = '';

        for ($i = 0; $i < self::KEY_LENGTH; $i++) {
            $key .= $characters[random_int(0, $charactersLength - 1)] ??
                $characters[mt_rand(0, $charactersLength - 1)];
        }

        return $key;
    }


    private function generateHash(array $data): string
    {
        $hashString = implode(':', array_map('strval', $data));
        return strtoupper(hash(self::HASH_ALGORITHM, $hashString));
    }
}

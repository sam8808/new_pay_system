<?php

namespace App\Services\Wallet;

use Exception;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Currency;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransferService
{
    // Определяем константы для валидации
    private const VALIDATION_RULES = [
        'to_account' => ['required', 'string', 'exists:users,account'],
        'from_wallet_id' => ['required', 'exists:wallets,id'],
        'amount' => ['required', 'numeric'],
        'description' => ['nullable', 'string', 'max:255']
    ];

    // Приватные свойства для хранения данных перевода
    private string $toAccount;
    private int $fromWalletId;
    private float $amount;
    private ?string $description;
    private array $errors = [];
    private bool $fail = false;
    private ?Transaction $transaction = null;
    private ?Wallet $fromWallet = null;
    private ?Wallet $toWallet = null;
    private ?User $recipient = null;

    /**
     * Конструктор инициализирует все необходимые свойства из входных данных
     */
    public function __construct(array $data)
    {
        $this->toAccount = trim($data['to_account'] ?? '');
        $this->fromWalletId = (int)($data['from_wallet_id'] ?? 0);
        $this->amount = (float)($data['amount'] ?? 0);
        $this->description = !empty($data['description']) ? trim($data['description']) : null;
    }

    /**
     * Статический метод для создания экземпляра сервиса
     */
    public static function init(array $data): self
    {
        return new self($data);
    }

    /**
     * Выполняет валидацию данных перевода
     */
    public function validate(): bool
    {
        try {
            // Базовая валидация через Laravel Validator
            $validator = Validator::make(
                $this->getValidationData(),
                self::VALIDATION_RULES,
                $this->getValidationMessages()
            );

            if ($validator->fails()) {
                $this->errors = $validator->errors()->toArray();
                return false;
            }

            // Загружаем необходимые модели для бизнес-валидации
            $this->loadModels();

            // Проверяем бизнес-правила
            $this->validateBusinessRules();

            return true;
        } catch (Exception $e) {
            Log::error('Transfer validation error: ' . $e->getMessage(), [
                'from_wallet_id' => $this->fromWalletId,
                'to_account' => $this->toAccount,
                'amount' => $this->amount,
                'trace' => $e->getTraceAsString()
            ]);
            $this->errors = [$e->getMessage()];
            return false;
        }
    }

    /**
     * Выполняет сохранение перевода в базу данных
     */
    public function store(): void
    {
        try {
            DB::transaction(function () {
                // Создаем транзакцию
                $this->transaction = Transaction::create([
                    'uuid' => (string)Str::uuid(),
                    'user_id' => $this->fromWallet->user_id,
                    'wallet_id' => $this->fromWallet->id,
                    'currency_id' => $this->fromWallet->currency_id,
                    'amount' => $this->amount,
                    'fee' => 0,
                    'amount_in_base_currency' => $this->amount,
                    'type' => 'transfer',
                    'status' => 'completed',
                    'metadata' => $this->prepareMetadata()
                ]);

                // Обновляем балансы кошельков
                $this->fromWallet->decrement('balance', $this->amount);
                $this->toWallet->increment('balance', $this->amount);

                // Создаем входящую транзакцию для получателя
                Transaction::create([
                    'uuid' => (string)Str::uuid(),
                    'user_id' => $this->toWallet->user_id,
                    'wallet_id' => $this->toWallet->id,
                    'currency_id' => $this->toWallet->currency_id,
                    'amount' => $this->amount,
                    'fee' => 0,
                    'amount_in_base_currency' => $this->amount,
                    'type' => 'transfer',
                    'status' => 'completed',
                    'metadata' => $this->prepareRecipientMetadata()
                ]);
            });
        } catch (Exception $e) {
            $this->handleStoreError($e);
            throw $e;
        }
    }

    /**
     * Возвращает созданную транзакцию
     */
    public function transaction(): ?Transaction
    {
        return $this->transaction;
    }

    /**
     * Возвращает массив ошибок
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Возвращает статус ошибки
     */
    public function fail(): bool
    {
        return $this->fail;
    }

    public function getTransactionDetails(): array
    {
        if (!$this->transaction) {
            return [];
        }

        return [
            'uuid' => $this->transaction->uuid,
            'amount' => [
                'value' => $this->transaction->amount,
                'formatted' => number_format($this->transaction->amount, 8),
                'currency' => $this->fromWallet->currency->code,
                'symbol' => $this->fromWallet->currency->symbol
            ],
            'sender' => [
                'account' => $this->fromWallet->user->account,
                'wallet_id' => $this->fromWallet->id
            ],
            'recipient' => [
                'account' => $this->recipient->account,
                'wallet_id' => $this->toWallet->id
            ],
            'description' => $this->description,
            'status' => $this->transaction->status,
            'created_at' => $this->transaction->created_at
        ];
    }

    /**
     * Возвращает аккаунт получателя перевода
     */
    public function getRecipientAccount(): string
    {
        return $this->recipient->account;
    }

    /**
     * Возвращает код валюты кошелька получателя
     */
    public function getRecipientWalletCurrency(): string
    {
        return $this->toWallet->currency->code;
    }

    /**
     * Возвращает детали исходящего кошелька
     */
    public function getSourceWalletDetails(): array
    {
        return [
            'id' => $this->fromWallet->id,
            'balance' => $this->fromWallet->balance,
            'currency_code' => $this->fromWallet->currency->code,
            'currency_symbol' => $this->fromWallet->currency->symbol
        ];
    }

    /**
     * Возвращает код валюты исходящего кошелька
     */
    public function getSourceWalletCurrency(): string
    {
        return $this->fromWallet->currency->code;
    }

    /**
     * Возвращает модель получателя
     */
    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    /**
     * Подготавливает данные для валидации
     */
    private function getValidationData(): array
    {
        return [
            'to_account' => $this->toAccount,
            'from_wallet_id' => $this->fromWalletId,
            'amount' => $this->amount,
            'description' => $this->description,
        ];
    }

    /**
     * Возвращает сообщения об ошибках валидации
     */
    private function getValidationMessages(): array
    {
        return [
            'to_account.required' => __('Recipient account is required'),
            'to_account.exists' => __('Recipient account not found'),
            'from_wallet_id.required' => __('Sender wallet is required'),
            'from_wallet_id.exists' => __('Invalid sender wallet'),
            'amount.required' => __('Amount is required'),
            'amount.numeric' => __('Amount must be a number'),
            'amount.gt' => __('Amount must be greater than 0'),
        ];
    }

    /**
     * Загружает необходимые модели из базы данных
     */
    private function loadModels(): void
    {
        $this->fromWallet = Wallet::findOrFail($this->fromWalletId);
        $this->recipient = User::where('account', $this->toAccount)->firstOrFail();

        $this->toWallet = Wallet::where('user_id', $this->recipient->id)
            ->where('currency_id', $this->fromWallet->currency_id)
            ->where('is_active', true)
            ->firstOrFail();
    }

    /**
     * Проверяет бизнес-правила перевода
     */
    private function validateBusinessRules(): void
    {
        if (!$this->fromWallet->is_active) {
            throw new Exception(__('Your wallet is inactive'));
        }

        if ($this->fromWallet->user_id === $this->recipient->id) {
            throw new Exception(__('Cannot transfer to yourself'));
        }

        if ($this->fromWallet->balance < $this->amount) {
            throw new Exception(__('Insufficient funds'));
        }

        $currency = Currency::find($this->fromWallet->currency_id);

        if ($this->amount < $currency->min_amount) {
            throw new Exception(__('Amount is below minimum allowed'));
        }

        if ($currency->max_amount && $this->amount > $currency->max_amount) {
            throw new Exception(__('Amount exceeds maximum allowed'));
        }
    }

    /**
     * Подготавливает метаданные для транзакции отправителя
     */
    private function prepareMetadata(): array
    {
        return [
            'recipient_wallet_id' => $this->toWallet->id,
            'recipient_user_id' => $this->recipient->id,
            'recipient_account' => $this->recipient->account,
            'description' => $this->description,
            'transfer_type' => 'outgoing'
        ];
    }

    /**
     * Подготавливает метаданные для транзакции получателя
     */
    private function prepareRecipientMetadata(): array
    {
        return [
            'sender_wallet_id' => $this->fromWallet->id,
            'sender_user_id' => $this->fromWallet->user_id,
            'sender_account' => $this->fromWallet->user->account,
            'description' => $this->description,
            'transfer_type' => 'incoming',
            'original_transaction_id' => $this->transaction->id
        ];
    }

    /**
     * Обрабатывает ошибки сохранения
     */
    private function handleStoreError(Exception $e): void
    {
        $this->fail = true;
        $errorMessage = __('Error while processing transfer: ') . $e->getMessage();
        Log::error($errorMessage, [
            'from_wallet_id' => $this->fromWalletId,
            'to_account' => $this->toAccount,
            'amount' => $this->amount,
            'trace' => $e->getTraceAsString()
        ]);
        $this->errors = [__('Transfer error')];
    }
}

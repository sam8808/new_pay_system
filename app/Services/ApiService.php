<?php

namespace App\Services;

use App\Models\Merchant;
use Illuminate\Http\Request;


class ApiService
{
    public function __construct(
        public readonly Request $request,
        public readonly Merchant $merchant,
        private readonly SignatureService $signatureService
    ) {}

    public function validate(): void
    {
        $this->request->validate([
            'shop' => ['required'],
            'order' => ['required'],
            'amount' => ['required', 'numeric'],
            'user_identify' => ['required'],
            'currency' => ['required'],
            'signature' => ['required'],
        ]);
    }

    public function verifyHash(): bool
    {
        $data = [
            'shop' => $this->merchant->m_id,
            'order' => $this->request->post('order'),
            'amount' => $this->request->post('amount'),
            'currency' => $this->request->post('currency'),
        ];

        return $this->signatureService->verifySignature(
            $data,
            $this->request->post('signature'),
            $this->merchant->m_key
        );
    }

    public function merchantExists(): bool
    {
        return (bool)$this->merchant;
    }
}

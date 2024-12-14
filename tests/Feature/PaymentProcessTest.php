<?php

namespace Tests\Feature;

use Tests\TestCase;

class PaymentProcessTest extends TestCase
{
    public function test_create_payment()
    {
        $response = $this->postJson('/api', [
            'shop' => '173279007',
            'order' => 'test_order_1',
            'amount' => '100.00',
            'currency' => 'USD',
            'user_identify' => 'test_user',
            'handler' => 'process',
            'signature' => '586D38C7D2A6663F53608B4E4F662A3E8E240BD6BF1F749EC09401A2C86CA623',
        ]);

        $response->assertStatus(201); // Проверяем, что запрос был успешным
    }

    public function test_successful_payment_emulation()
    {
        $response = $this->postJson('/api/test/payment/process', [
            'external_id' => 'TEST_123',
            'card_number' => '4242424242424242',
            'expiry' => '12/25',
            'cvv' => '123',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']); // Проверяем, что статус оплаты "успешный"
    }

    public function test_payment_status_check()
    {
        $response = $this->getJson('/api/payments/1/status');

        $response->assertStatus(200);
        $response->assertJsonStructure(['status']); // Убедимся, что ответ содержит поле "status"
    }

    public function test_failed_payment_emulation()
    {
        $response = $this->postJson('/api/test/payment/process', [
            'external_id' => 'TEST_123',
            'card_number' => '4000000000000002',
            'expiry' => '12/25',
            'cvv' => '123',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['status' => 'failed']);
    }

    public function test_insufficient_funds_payment()
    {
        $response = $this->postJson('/api/test/payment/process', [
            'external_id' => 'TEST_123',
            'card_number' => '4000000000009995',
            'expiry' => '12/25',
            'cvv' => '123',
        ]);

        $response->assertStatus(400);
        $response->assertJson(['status' => 'insufficient_funds']);
    }
}

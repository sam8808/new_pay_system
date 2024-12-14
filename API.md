# Payment System API Documentation

## Initialization

To start accepting payments, send POST request to `/api/payments/initialize`:

```json
{
    "shop": "your_shop_id",
    "amount": 100.00,
    "currency": "USD",
    "order": "order_123",
    "signature": "GENERATED_SIGNATURE"
}

### 1. Запрос от мерчанта для создания платежа
POST http://127.0.0.1:8000/api
Content-Type: application/json

{
    "shop": "173279007",
    "order": "test_order_1",
    "amount": "100.00",
    "currency": "USD",
    "user_identify": "test_user",
    "handler": "process",
    "signature": "586D38C7D2A6663F53608B4E4F662A3E8E240BD6BF1F749EC09401A2C86CA623"
}

### 2. Эмуляция тестовой оплаты (успешная)
POST http://127.0.0.1:8000/api/test/payment/process
Content-Type: application/json

{
    "external_id": "TEST_123",
    "card_number": "4242424242424242",
    "expiry": "12/25",
    "cvv": "123"
}

### 3. Проверка статуса платежа
GET http://127.0.0.1:8000/api/payments/1/status

### 4. Эмуляция неуспешной оплаты
POST http://127.0.0.1:8000/api/test/payment/process
Content-Type: application/json

{
    "external_id": "TEST_123",
    "card_number": "4000000000000002",
    "expiry": "12/25",
    "cvv": "123"
}

### 5. Эмуляция недостаточно средств
POST http://127.0.0.1:8000/api/test/payment/process
Content-Type: application/json

{
    "external_id": "TEST_123",
    "card_number": "4000000000009995",
    "expiry": "12/25",
    "cvv": "123"
}
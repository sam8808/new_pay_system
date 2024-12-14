<?php

use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\GatewayPaymentController;
use App\Http\Controllers\API\WebhookController;
use App\Http\Controllers\Ticket\TicketController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Ticket\MessageController;
// Статус платежа
// Route::get('payments/{payment}/status', [ApiController::class, 'status'])
//     ->name('api.payment.status');


/**
 *
 * ## 5. Процесс обработки платежа

    1. Пользователь инициирует платёж на сайте мерчанта
    2. Мерчант отправляет запрос на создание платежа в шлюз
    3. Шлюз формирует запрос к платёжной системе
    4. Платёжная система возвращает ссылку на оплату
    5. Шлюз передаёт ссылку мерчанту
    6. Мерчант перенаправляет пользователя на оплату
    7. После завершения платежа, платёжная система отправляет webhook-уведомление
    8. Шлюз обрабатывает уведомление и отправляет результат мерчанту

 * */
Route::prefix('payments')->group(function () {
    // 2-5 create a payment
    Route::post('/create', [GatewayPaymentController::class, 'createPay'])->name('payments.create');

    // get payment by ID
    Route::get('/{id}', [GatewayPaymentController::class, 'getPayment'])->name('payments.show');
});

// handle payment system webhook
Route::post('/webhook/payment-system', [WebhookController::class, 'handle'])->name('webhooks.paymentSystem');

Route::prefix('tickets')->group(function () {
    Route::post('/create', [TicketController::class, 'createTicket'])->name('tickets.create');
    Route::get('/{id}', [TicketController::class, 'getAllMessages'])->name('tickets.create');

    Route::prefix('/messages/')->group(function () {
        Route::post('/create', [MessageController::class, 'createMessage'])->name('messages.create');
    });
});


<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\GatewayPaymentController;
use App\Http\Controllers\API\WebhookController;
use App\Http\Controllers\API\GatewayController;
use App\Http\Controllers\Ticket\TicketController;
use \App\Http\Controllers\Ticket\MessageController;
// Статус платежа
// Route::get('payments/{payment}/status', [ApiController::class, 'status'])
//     ->name('api.payment.status');

use App\Http\Controllers\API\TinkoffGatewayController;
use App\Http\Controllers\API\SberGatewayController;
use App\Http\Controllers\API\CryptomusGatewayController;

/**
 * ## Payment Processing Workflow
 *
 * 1. The user initiates a payment on the merchant's website.
 * 2. The merchant sends a payment creation request to the gateway.
 * 3. The gateway forwards the request to the payment system.
 * 4. The payment system returns a payment link.
 * 5. The gateway provides the payment link to the merchant.
 * 6. The merchant redirects the user to the payment page.
 * 7. After completing the payment, the payment system sends a webhook notification.
 * 8. The gateway processes the notification and informs the merchant of the result.
 */

// Payment routes for various payment gateways
Route::prefix('payments')->group(function () {
    /**
     * Create a new payment.
     * Sends a payment creation request to the gateway and returns the payment details.
     *
     * @route POST /payments/create
     * @name payments.create
     * @uses GatewayPaymentController::createPay
     */
    Route::post('/create', [GatewayPaymentController::class, 'createPay'])->name('payments.create');

    /**
     * Get payment details by ID.
     * Fetches the details of a specific payment.
     *
     * @route GET /payments/{id}
     * @name payments.show
     * @uses GatewayPaymentController::getPayment
     */
    Route::get('/{id}', [GatewayPaymentController::class, 'getPayment'])->name('payments.show');

    // Tinkoff payment routes
    /**
     * Handle Tinkoff payment webhook notifications.
     *
     * @route POST /payments/tinkoff/webhook
     * @name tinkoff.webhook
     * @uses TinkoffGatewayController::webhook
     */
    Route::post('/tinkoff/webhook', [TinkoffGatewayController::class, 'webhook'])->name('tinkoff.webhook');

    /**
     * Handle Tinkoff payment success redirection.
     *
     * @route GET /payments/tinkoff/success
     * @name tinkoff.success
     * @uses TinkoffGatewayController::success
     */
    Route::get('/tinkoff/success', [TinkoffGatewayController::class, 'success'])->name('tinkoff.success');

    /**
     * Handle Tinkoff payment failure redirection.
     *
     * @route GET /payments/tinkoff/fail
     * @name tinkoff.fail
     * @uses TinkoffGatewayController::fail
     */
    Route::get('/tinkoff/fail', [TinkoffGatewayController::class, 'fail'])->name('tinkoff.fail');

    // Sber payment routes
    /**
     * Handle Sber payment webhook notifications.
     *
     * @route POST /payments/sber/webhook
     * @name sber.webhook
     * @uses SberGatewayController::webhook
     */
    Route::post('sber/webhook', [SberGatewayController::class, 'webhook'])->name('sber.webhook');

    /**
     * Handle Sber payment success redirection.
     *
     * @route GET /payments/sber/success
     * @name sber.success
     * @uses SberGatewayController::success
     */
    Route::get('sber/success', [SberGatewayController::class, 'success'])->name('sber.success');

    /**
     * Handle Sber payment failure redirection.
     *
     * @route GET /payments/sber/fail
     * @name sber.fail
     * @uses SberGatewayController::fail
     */
    Route::get('sber/fail', [SberGatewayController::class, 'fail'])->name('sber.fail');

    // Cryptomus payment routes
    /**
     * Generate a payment link using the Cryptomus API.
     *
     * @route POST /payments/cryptomus/payment-link
     * @uses CryptomusGatewayController::getPaymentLink
     */
    Route::post('cryptomus/payment-link', [CryptomusGatewayController::class, 'getPaymentLink']);

    /**
     * Handle Cryptomus payment webhook notifications.
     *
     * @route POST /payments/cryptomus/webhook
     * @name cryptomus.webhook
     * @uses CryptomusGatewayController::webhook
     */
    Route::post('cryptomus/webhook', [CryptomusGatewayController::class, 'webhook'])->name('cryptomus.webhook');

    /**
     * Handle Cryptomus payment success redirection.
     *
     * @route GET /payments/cryptomus/success
     * @name cryptomus.success
     * @uses CryptomusGatewayController::success
     */
    Route::get('cryptomus/success', [CryptomusGatewayController::class, 'success'])->name('cryptomus.success');

    /**
     * Handle Cryptomus payment failure redirection.
     *
     * @route GET /payments/cryptomus/fail
     * @name cryptomus.fail
     * @uses CryptomusGatewayController::fail
     */
    Route::get('cryptomus/fail', [CryptomusGatewayController::class, 'fail'])->name('cryptomus.fail');
});

/**
 * Handle payment system webhook notifications.
 * Processes webhook payloads from various payment systems.
 *
 * @route POST /webhook/payment-system
 * @name webhooks.paymentSystem
 * @uses WebhookController::handle
 */
Route::post('/webhook/payment-system', [WebhookController::class, 'handle'])->name('webhooks.paymentSystem');

Route::middleware("auth:sanctum")->group(function () {
    Route::prefix('tickets')->group(function () {
        Route::get('/', [TicketController::class, 'index']);
        Route::put('/assign', [TicketController::class, 'assign']);
        Route::put('/{id}', [TicketController::class, 'update']);
        Route::post('/create', [TicketController::class, 'createTicket'])->name('tickets.create');
        Route::get('/{id}', [TicketController::class, 'getAllMessages'])->name('tickets.create');

        Route::prefix('/messages/')->group(function () {
            Route::post('/create', [MessageController::class, 'createMessage'])->name('messages.create');
            Route::post('/send', [MessageController::class, 'sendMessage'])->name('messages.send');
            Route::get('/{id}', [MessageController::class, 'index'])->name('messages.create');
        });
    });
});
Route::middleware("auth.auth")->group(function () {
    Route::prefix('admin/tickets')->group(function () {
    });
});

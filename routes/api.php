<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\API\ApiController;
use App\Http\Controllers\API\ApiHandlerController;
use App\Http\Controllers\API\TestPaymentController;

Route::post('', ApiHandlerController::class)
    ->middleware('api');


Route::get('', [ApiController::class, 'index'])
    ->name('api.index');

Route::post('pay', [ApiController::class, 'pay'])
    ->name('api.pay');

Route::post('pay/{id}/send-order', [ApiController::class, 'sendOrder'])
    ->name('api.pay.sendOrder');

Route::post('pay/confirm', [ApiController::class, 'payConfirm'])
    ->name('api.pay.confirm');

Route::post('pay/{id}/confirm/manual', [ApiController::class, 'payConfirmManual'])
    ->name('api.pay.confirm.manual');

Route::get('redirect/{action}', [ApiController::class, 'redirect'])
    ->name('api.pay.redirect');


Route::get('test', [TestController::class, 'test'])->name('merchant.test');


Route::prefix('api/test')->group(function () {
    Route::post('/payments', [TestPaymentController::class, 'create']);
    Route::post('/payments/{payment}/process', [TestPaymentController::class, 'process']);
});

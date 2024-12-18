<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestMerchantController;
use App\Http\Controllers\TestPaymentController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\MerchantCouponsController;

Route::get('/', [HomeController::class, 'index'])->name('home');


Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'index'])
        ->name('register');

    Route::post('register', [RegisterController::class, 'store'])
        ->name('register.store');

    Route::get('login', [LoginController::class, 'index'])
        ->name('login');

    Route::post('login', [LoginController::class, 'store'])
        ->name('login.store');


    //     Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    //         ->name('password.request');

    //     Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    //         ->name('password.email');

//         Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    //         ->name('password.reset');

    //     Route::post('reset-password', [NewPasswordController::class, 'store'])
    //         ->name('password.store');
    // });

    // Route::middleware('auth')->group(function () {
    //     Route::get('verify-email', EmailVerificationPromptController::class)
    //         ->name('verification.notice');

    //     Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
    //         ->middleware(['signed', 'throttle:6,1'])
    //         ->name('verification.verify');

    //     Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    //         ->middleware('throttle:6,1')
    //         ->name('verification.send');

    //     Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
    //         ->name('password.confirm');

    //     Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    //     Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('test/merchant/webhook', [TestMerchantController::class, 'webhook'])
        ->name('test.merchant');
    Route::get('invite/{ref_code}', [HomeController::class, 'invite']);
});
Route::post('logout', [LoginController::class, 'logout'])
    ->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('test/merchant', [TestMerchantController::class, 'index'])
        ->name('test.merchant');

    Route::get('test-payment', [TestPaymentController::class, 'index'])
        ->name('test.payment');

    Route::get('merchant-coupon/use', [MerchantCouponsController::class, 'use'])->name('merchant-coupon.use');
    Route::post('merchant-coupon/verify', [MerchantCouponsController::class, 'verify'])->name('merchant-coupon.verify');
    Route::resource('merchant-coupon', MerchantCouponsController::class);
});



    // Управление двухфакторной аутентификацией
    Route::prefix('2fa')->group(function () {
        Route::post('toggle', [TwoFactorController::class, 'toggle'])
            ->name('2fa.toggle');
        Route::post('verify', [TwoFactorController::class, 'verify'])
            ->name('2fa.verify');
        Route::post('send', [TwoFactorController::class, 'send'])
                    ->name('2fa.send');
    });



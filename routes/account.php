<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Account\MainController;
use App\Http\Controllers\Account\OrderController;
use App\Http\Controllers\Account\DepositController;
use App\Http\Controllers\Account\ProfileController;
use App\Http\Controllers\Account\MerchantController;
use App\Http\Controllers\Account\WithdrawalController;
use App\Http\Controllers\Account\TransactionController;




Route::prefix('account')->middleware('auth')->group(function () {

    Route::get('', [MainController::class, 'index'])
        ->name('dashboard');

    Route::get('partners', [MainController::class, 'partners'])
        ->name('partners');

    Route::prefix('profile')->group(function () {
        Route::get('', [ProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::put('update', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::post('updateContacts', [ProfileController::class, 'updateContacts'])
            ->name('profile.update.contact');

        Route::delete('destroy', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });

    Route::prefix('merchant')->group(function () {
        Route::get('', [MerchantController::class, 'index'])
            ->name('merchant');

        Route::get('{merchant}/show', [MerchantController::class, 'show'])
            ->name('merchant.show');

        Route::get('create', [MerchantController::class, 'create'])
            ->name('merchant.create');

        Route::post('store', [MerchantController::class, 'store'])
            ->name('merchant.store');

        Route::get('{merchant}/edit', [MerchantController::class, 'edit'])
            ->name('merchant.edit');

        Route::post('{merchant}/update', [MerchantController::class, 'update'])
            ->name('merchant.update');

        Route::post('{merchant}/activate', [MerchantController::class, 'activateOrDeactivate'])
            ->name('merchant.activate');

        Route::post('{merchant}/delete', [MerchantController::class, 'destroy'])
            ->name('merchant.destroy');
    });

    Route::prefix('deposit')->group(function () {
        Route::get('', [DepositController::class, 'index'])
            ->name('deposit');

        Route::post('', [DepositController::class, 'store'])
            ->name('deposit.store');

        Route::get('{uuid}', [DepositController::class, 'show'])
            ->name('deposit.show');
    });

    Route::prefix('withdrawal')->group(function () {
        Route::get('', [WithdrawalController::class, 'create'])
            ->name('withdrawal');

        Route::get('create', [WithdrawalController::class, 'create'])
            ->name('withdrawal.create');

        Route::post('', [WithdrawalController::class, 'store'])
            ->name('withdrawal.store');
    });

    Route::get('transaction', [TransactionController::class, 'index'])
        ->name('transaction');

    Route::get('{id}/transaction', [TransactionController::class, 'show'])
        ->name('transaction.show');

    Route::get('orders', [OrderController::class, 'index'])
        ->name('user.orders');
});

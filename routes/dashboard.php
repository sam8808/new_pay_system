<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\MainController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\MerchantController;
use App\Http\Controllers\Dashboard\WithdrawalController;



Route::prefix('dashboard')->middleware('auth')->group(function () {

    Route::get('', [MainController::class, 'index'])
        ->name('dashboard');

    Route::get('transactions', [MainController::class, 'transactions'])
        ->name('transactions');

    Route::get('{id}/transaction', [MainController::class, 'transaction'])
        ->name('transaction');

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

        Route::get('{id}/show', [MerchantController::class, 'show'])
            ->name('merchant.show');

        Route::get('create', [MerchantController::class, 'create'])
            ->name('merchant.create');

        Route::post('store', [MerchantController::class, 'store'])
            ->name('merchant.store');

        Route::get('{id}/edit', [MerchantController::class, 'edit'])
            ->name('merchant.edit');

        Route::post('{id}/update', [MerchantController::class, 'update'])
            ->name('merchant.update');

        Route::post('{id}/activate', [MerchantController::class, 'activateOrDeactivate'])
            ->name('merchant.activate');

        Route::post('{id}/delete', [MerchantController::class, 'destroy'])
            ->name('merchant.destroy');
    });

    Route::prefix('withdrawal')->group(function () {
        Route::get('', [WithdrawalController::class, 'create'])
            ->name('withdrawal');

        Route::post('', [WithdrawalController::class, 'store'])
            ->name('withdrawal.store');
    });

    Route::get('orders', [OrderController::class, 'index'])
        ->name('user.orders');
});

@extends('layouts.admin')

@section('title', 'Платежные системы')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-gray-800 text-2xl font-medium">{{ __('Добавление валюты') }}</h3>
            <a href="{{ route('admin.currency') }}" 
               class="text-sm font-medium text-gray-600 hover:text-yellow-500 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                {{ __('Назад') }}
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <form action="{{route('admin.currency.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Basic Fields -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Название') }}</label>
                        <input type="text"
                               name="title"
                               id="title"
                               value="{{ old('title') }}"
                               class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 text-sm">
                        @error('title')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Код') }}</label>
                        <input type="text"
                               name="code"
                               id="code"
                               value="{{ old('code') }}"
                               class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 text-sm">
                        @error('code')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Тип') }}</label>
                        <select name="type"
                                id="type"
                                class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 text-sm">
                            <option value="">{{ __('Выберите тип') }}</option>
                            <option value="fiat" {{ old('type') == 'fiat' ? 'selected' : '' }}>{{ __('Фиатная') }}</option>
                            <option value="crypto" {{ old('type') == 'crypto' ? 'selected' : '' }}>{{ __('Крипто') }}</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="symbol" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Символ') }}</label>
                        <input type="text"
                               name="symbol"
                               id="symbol"
                               value="{{ old('symbol') }}"
                               class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 text-sm">
                        @error('symbol')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Icon Upload -->
                <div x-data="{ imageUrl: '', imageUploaded: false }" class="mb-6">
                    <div class="flex items-center gap-4">
                        <div class="shrink-0" x-show="imageUploaded">
                            <img x-bind:src="imageUrl" class="h-12 w-12 rounded-lg object-cover bg-gray-50" alt="Preview">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Иконка') }}</label>
                            <input type="file"
                                   name="icon"
                                   accept="image/*"
                                   x-on:change="imageUploaded = true; imageUrl = URL.createObjectURL($event.target.files[0])"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:bg-yellow-400 file:text-black hover:file:bg-yellow-500 cursor-pointer">
                        </div>
                    </div>
                    @error('icon')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-yellow-400 text-black text-sm font-medium rounded-lg hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition-colors">
                        {{ __('Добавить валюту') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
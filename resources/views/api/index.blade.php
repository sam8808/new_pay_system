@extends('layouts.app')

@section('title', 'Оплата')

@section('content')
<div class="container mx-auto px-6 py-8 lg:w-2/3">
    <div class="flex flex-col items-center">
        <div class="w-full">
            <h3 class="text-center text-3xl font-bold text-gray-800">{{ __('Оплата счета') }}</h3>
            <p class="mt-2 text-center text-lg text-gray-600">{{ __('Вам предъявлен счет на оплату от') }} <a href="{{ $shop->base_url }}" class="font-semibold text-blue-500 hover:underline" target="_blank">{{ $shop->title }}</a></p>
        </div>

        <div class="w-full mt-10 bg-white p-8 rounded-lg shadow-lg">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 text-sm">
                <div class="flex justify-between border-b pb-4">
                    <span class="font-semibold text-gray-700">{{ __('Получатель') }}</span>
                    <span class="text-gray-500">{{ $shop->base_url }}</span>
                </div>

                <div class="flex justify-between border-b pb-4">
                    <span class="font-semibold text-gray-700">{{ __('Счет') }}</span>
                    <span class="text-gray-500">{{ $data->order }}</span>
                </div>

                <div class="flex justify-between border-b pb-4">
                    <span class="font-semibold text-gray-700">{{ __('Валюта') }}</span>
                    <span class="text-gray-500">{{ __($data->currency) }}</span>
                </div>

                <div class="flex justify-between text-xl border-b pb-4">
                    <span class="font-semibold text-gray-700">{{ __('Сумма') }}</span>
                    <span class="text-gray-700">{{ $data->amount }}</span>
                </div>
            </div>
        </div>

        <div class="w-full mt-8">
            <h3 class="text-2xl font-semibold text-gray-800 text-center">{{ __('Выберите платежную систему') }}</h3>
            <form method="POST" action="{{ route('api.pay') }}" class="mt-6">
                @csrf
                <div class="flex flex-wrap justify-center items-center gap-6">
                    @foreach ($paymentSystems as $paymentSystem)
                    <label class="cursor-pointer transition-all">
                        <input type="radio" class="hidden" name="payment_system" value="{{ $paymentSystem->id }}" />
                        <div class="max-w-xs w-full bg-white rounded-lg border p-4 shadow hover:shadow-md hover:border-blue-500 transition-all">
                            <div class="flex items-center justify-between">
                                <p class="text-lg font-semibold text-gray-600 uppercase">{{ $paymentSystem->title }}</p>
                                <img src="{{ asset('storage/' . $paymentSystem->logo) }}" alt="{{ $paymentSystem->title }}" class="w-12 rounded">
                            </div>
                        </div>
                    </label>
                    @endforeach
                    @error('payment_system')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mt-8 flex justify-center">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-10 rounded-full shadow-lg transition-all focus:outline-none focus:ring-4 focus:ring-blue-300">{{ __('Продолжить') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<a href="https://wa.me/?phone={{ $shop->phone }}" class="fixed bottom-5 right-5 bg-green-500 p-4 rounded-full shadow-lg hover:bg-green-600 transition-all" target="_blank">
    <svg height="36px" width="36px" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 58 58">
        <g>
            <path style="fill:#2CB742;" d="M0,58l4.988-14.963C2.457,38.78,1,33.812,1,28.5C1,12.76,13.76,0,29.5,0S58,12.76,58,28.5
                S45.24,57,29.5,57c-4.789,0-9.299-1.187-13.26-3.273L0,58z" />
            <path style="fill:#FFFFFF;" d="M47.683,37.985c-1.316-2.487-6.169-5.331-6.169-5.331c-1.098-0.626-2.423-0.696-3.049,0.42
                c0,0-1.577,1.891-1.978,2.163c-1.832,1.241-3.529,1.193-5.242-0.52l-3.981-3.981l-3.981-3.981c-1.713-1.713-1.761-3.41-0.52-5.242
                c0.272-0.401,2.163-1.978,2.163-1.978c1.116-0.627,1.046-1.951,0.42-3.049c0,0-2.844-4.853-5.331-6.169
                c-1.058-0.56-2.357-0.364-3.203,0.482l-1.758,1.758c-5.577,5.577-2.831,11.873,2.746,17.45l5.097,5.097l5.097,5.097
                c5.577,5.577,11.873,8.323,17.45,2.746l1.758-1.758C48.048,40.341,48.243,39.042,47.683,37.985z" />
        </g>
    </svg>
</a>
@endsection
@extends('layouts.admin')

@section('title', 'Платежные системы')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-8">
        <h3 class="text-gray-800 text-3xl font-semibold">{{ __('Платежные системы')  }}</h3>
        <a href="{{ route('admin.currency.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-yellow-400 hover:bg-black hover:text-yellow-400 text-black font-semibold rounded-lg transition duration-300">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            {{ __('Добавить валюту') }}
        </a>
    </div>

    <!-- Search and Filter Section -->
    <!-- <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="relative">
                <input type="text" 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400" 
                       placeholder="{{__('Поиск валюты...')}}">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>
            <select class="border border-gray-200 rounded-lg focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400">
                <option value="">{{__('Фильтр по статусу')}}</option>
                <option value="active">{{__('Активные')}}</option>
                <option value="inactive">{{__('Неактивные')}}</option>
            </select>
        </div>
    </div> -->

    <!-- Table Section -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-4 font-semibold text-gray-700">{{__('Иконка')}}</th>
                    <th scope="col" class="px-6 py-4 font-semibold text-gray-700">{{__('Название')}}</th>
                    <th scope="col" class="px-6 py-4 font-semibold text-gray-700">{{__('Код')}}</th>
                    <th scope="col" class="px-6 py-4 font-semibold text-gray-700">{{__('Символ')}}</th>
                    <th scope="col" class="px-6 py-4 font-semibold text-gray-700">{{__('Действия')}}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($currencies as $currency)
                <tr class="hover:bg-gray-50 transition duration-150">
                    <td class="px-6 py-4">
                        <img class="w-10 h-10 rounded-lg object-cover" 
                             src="{{ asset('storage/' . $currency->icon) }}" 
                             alt="{{ $currency->title }}">
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1.5 text-sm font-semibold bg-yellow-100 text-yellow-800 rounded-lg">
                            {{__($currency->title)}}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1.5 text-sm font-semibold bg-sky-100 text-sky-800 rounded-lg">
                            {{__($currency->code)}}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-700">
                        {{ __($currency->symbol) }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <form action="{{route('admin.currency.changeStatus', $currency)}}" method="POST" class="inline-block">
                                @csrf
                                <button type="submit" 
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 {{ $currency->status ? 'bg-red-500' : 'bg-green-500' }}">
                                    <span class="sr-only">{{__('Изменить статус')}}</span>
                                    <span aria-hidden="true" 
                                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $currency->status ? 'translate-x-5' : 'translate-x-0' }}">
                                    </span>
                                </button>
                            </form>

                            <a href="{{route('admin.ps.edit', $currency->id)}}" 
                               class="inline-flex items-center px-3 py-1.5 bg-yellow-400 hover:bg-yellow-500 text-black font-medium rounded-lg transition duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                {{__('Редактировать')}}
                            </a>

                            <button type="button" 
                                    class="inline-flex items-center px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white font-medium rounded-lg transition duration-150">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                {{__('Удалить')}}
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    <div class="mt-6">
        {{ $currencies->links() }}
    </div>
</div>
@endsection
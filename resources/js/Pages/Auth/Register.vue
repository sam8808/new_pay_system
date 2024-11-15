<template>
    <div
        class="min-h-screen bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-100 via-violet-100 to-gray-100 flex flex-col justify-center items-center p-6"
    >
        <!-- Floating shapes background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-r from-blue-400/30 to-indigo-400/30 rounded-full filter blur-3xl"
            ></div>
            <div
                class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-gradient-to-r from-violet-400/30 to-purple-400/30 rounded-full filter blur-3xl"
            ></div>
        </div>

        <!-- Main container -->
        <div class="relative w-full max-w-md">
            <!-- Card -->
            <div
                class="bg-white/80 backdrop-blur-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-3xl p-8 border border-white/20"
            >
                <!-- Header section -->
                <div class="mb-10 space-y-2">
                    <h1
                        class="text-4xl font-bold text-center bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 bg-clip-text text-transparent"
                    >
                        Добро пожаловать
                    </h1>
                    <p class="text-gray-500 text-center font-medium">
                        Создайте аккаунт для полного доступа
                    </p>
                </div>

                <!-- Form section -->
                <div class="space-y-6">
                    <!-- Username input group -->
                    <div class="space-y-2 group">
                        <label
                            class="text-sm font-semibold text-gray-600 ml-1 group-focus-within:text-blue-600 transition-colors"
                        >
                            Ваше имя
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-400">
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    ></path>
                                </svg>
                            </span>
                            <input
                                name="username"
                                type="text"
                                class="w-full h-12 pl-12 pr-4 bg-white/50 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200/50 transition duration-200 ease-in-out placeholder-gray-400 shadow-sm"
                                placeholder="Джон Доу"
                            />
                        </div>
                        <p class="text-red-500 text-xs ml-1">{{ message }}</p>
                    </div>

                    <!-- Email input group -->
                    <div class="space-y-2 group">
                        <label
                            class="text-sm font-semibold text-gray-600 ml-1 group-focus-within:text-blue-600 transition-colors"
                        >
                            Email адрес
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-400">
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                    ></path>
                                </svg>
                            </span>
                            <input
                                name="email"
                                type="text"
                                class="w-full h-12 pl-12 pr-4 bg-white/50 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200/50 transition duration-200 ease-in-out placeholder-gray-400 shadow-sm"
                                placeholder="email@example.com"
                            />
                        </div>
                        <p class="text-red-500 text-xs ml-1">{{ message }}</p>
                    </div>

                    <!-- Password input group -->
                    <div class="space-y-2 group">
                        <label
                            class="text-sm font-semibold text-gray-600 ml-1 group-focus-within:text-blue-600 transition-colors"
                        >
                            Пароль
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-400">
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    ></path>
                                </svg>
                            </span>
                            <input
                                name="password"
                                type="password"
                                class="w-full h-12 pl-12 pr-4 bg-white/50 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200/50 transition duration-200 ease-in-out placeholder-gray-400 shadow-sm"
                                placeholder="Минимум 8 символов"
                            />
                        </div>
                        <p class="text-red-500 text-xs ml-1">{{ message }}</p>
                    </div>

                    <!-- Password confirmation input group -->
                    <div class="space-y-2 group">
                        <label
                            class="text-sm font-semibold text-gray-600 ml-1 group-focus-within:text-blue-600 transition-colors"
                        >
                            Подтверждение пароля
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-gray-400">
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="1.5"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    ></path>
                                </svg>
                            </span>
                            <input
                                name="password_confirmation"
                                type="password"
                                class="w-full h-12 pl-12 pr-4 bg-white/50 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-200/50 transition duration-200 ease-in-out placeholder-gray-400 shadow-sm"
                                placeholder="Повторите пароль"
                            />
                        </div>
                        <p class="text-red-500 text-xs ml-1">{{ message }}</p>
                    </div>

                    <!-- Terms agreement -->
                    <div class="flex items-start space-x-3 mt-6">
                        <input
                            v-model="form.agreement"
                            type="checkbox"
                            class="mt-1 rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-200/50"
                        />
                        <p class="text-sm text-gray-500">
                            Я согласен с
                            <a
                                href="#"
                                class="text-blue-600 hover:text-blue-700"
                                >Условиями использования</a
                            >
                            и
                            <a
                                href="#"
                                class="text-blue-600 hover:text-blue-700"
                                >Политикой конфиденциальности</a
                            >
                        </p>
                    </div>

                    <!-- Action buttons -->
                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 text-white py-3.5 rounded-xl font-bold hover:from-blue-700 hover:via-indigo-700 hover:to-violet-700 transform hover:-translate-y-0.5 transition duration-200 ease-in-out shadow-[0_4px_20px_rgba(0,0,0,0.1)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.15)] active:transform-none flex justify-center items-center space-x-2"
                    >
                        <span>Зарегистрироваться</span>
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6"
                            ></path>
                        </svg>
                    </button>

                    <!-- Login link -->
                    <div class="text-center">
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-700 font-medium transition duration-200"
                        >
                            <span>←</span>
                            <span>Уже есть аккаунт? Войти</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Brand badge -->
            <div
                class="absolute -top-2 -right-2 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full shadow-lg border border-white/20 flex items-center space-x-1"
            >
                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                <span class="text-xs font-medium text-gray-600"
                    >Защищено SSL</span
                >
            </div>
        </div>

        <!-- Footer section -->
        <div class="mt-8 text-center space-y-4">
            <div class="flex justify-center space-x-6">
                <div class="flex items-center space-x-1">
                    <svg
                        class="w-5 h-5 text-yellow-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                    </svg>
                    <svg
                        class="w-5 h-5 text-yellow-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                    </svg>
                    <svg
                        class="w-5 h-5 text-yellow-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                    </svg>
                    <svg
                        class="w-5 h-5 text-yellow-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                    </svg>
                    <svg
                        class="w-5 h-5 text-yellow-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                    </svg>
                    <span class="text-sm font-medium text-gray-500 ml-2"
                        >4.9/5 от наших пользователей</span
                    >
                </div>
            </div>

            <!-- Trust badges -->
            <div class="flex justify-center items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <svg
                        class="w-5 h-5 text-green-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                        />
                    </svg>
                    <span class="text-sm text-gray-500"
                        >Безопасная регистрация</span
                    >
                </div>
                <div class="flex items-center space-x-2">
                    <svg
                        class="w-5 h-5 text-blue-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                        />
                    </svg>
                    <span class="text-sm text-gray-500">Защита данных</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg
                        class="w-5 h-5 text-purple-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"
                        />
                    </svg>
                    <span class="text-sm text-gray-500">Быстрая поддержка</span>
                </div>
            </div>

            <!-- Copyright -->
            <p class="text-sm text-gray-500">
                © 2024 {{ companyName }}. Все права защищены.
            </p>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue';

const message = "Test Message";
const companyName = "Payment System";


const form = reactive({
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    agreement: false,
})

</script>

<style></style>

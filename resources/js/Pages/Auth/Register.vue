<script setup>
import { reactive, ref, defineProps } from "vue";
import FormInput from "../../Components/FormInput.vue";
import { CiUser, CiMail, CiLock, CoUserPlus } from "@kalimahapps/vue-icons";
import { router, usePage } from "@inertiajs/vue3";
import Alert from "../../Components/Alert.vue";

defineProps({
    errors: {
        type: Object,
    },
});

const page = usePage();

const companyName = "Payment System";
const loading = ref(false);

const form = reactive({
    username: "",
    email: "",
    password: "",
    password_confirmation: "",
    agreement: false,
});

const submit = () => {
    loading.value = true;
    router.post(route("register.store"), form, {
        preserveScroll: true,
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>

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
                <form @submit.prevent="submit" class="space-y-4">
                    <FormInput
                        v-model="form.username"
                        title="Имя пользователя"
                        type="text"
                        :message="errors.username"
                        :icon="CiUser"
                        placeholder="Введите имя пользователя"
                        autocomplete="username"
                    />

                    <FormInput
                        v-model="form.email"
                        title="Email"
                        type="email"
                        :message="errors.email"
                        :icon="CiMail"
                        placeholder="Введите email"
                        autocomplete="email"
                    />

                    <FormInput
                        v-model="form.password"
                        title="Пароль"
                        type="password"
                        :message="errors.password"
                        :icon="CiLock"
                        placeholder="Введите пароль"
                        :minLength="8"
                        autocomplete="new-password"
                    />

                    <FormInput
                        v-model="form.password_confirmation"
                        title="Подтверждение пароля"
                        type="password"
                        :message="errors.password_confirmation"
                        :icon="CiLock"
                        placeholder="Подтвердите пароль"
                        :minLength="8"
                        autocomplete="new-password"
                    />
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
                        :disabled="!form.agreement"
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 text-white py-3.5 rounded-xl font-bold hover:from-blue-700 hover:via-indigo-700 hover:to-violet-700 transform hover:-translate-y-0.5 transition duration-200 ease-in-out shadow-[0_4px_20px_rgba(0,0,0,0.1)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.15)] active:transform-none disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:transform-none disabled:hover:shadow-[0_4px_20px_rgba(0,0,0,0.1)] flex justify-center items-center space-x-2"
                    >
                        <span>Зарегистрироваться</span>
                        <!-- Опционально: можно добавить иконку -->
                        <component
                            :is="CoUserPlus"
                            class="w-5 h-5"
                            v-if="!loading"
                        />
                        <!-- Опционально: индикатор загрузки -->
                        <svg
                            v-if="loading"
                            class="animate-spin h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            />
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

                    <div class="text-center">
                        <Link
                            :href="route('home')"
                            class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-700 font-medium transition duration-200"
                        >
                            <span>На главную</span>
                        </Link>
                    </div>
                </form>
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

<style></style>

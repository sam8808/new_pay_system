<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

const form = useForm({
    username: "",
    password: "",
});

const loading = ref(false);
const showPassword = ref(false);

const togglePassword = () => {
    showPassword.value = !showPassword.value;
};

const submit = () => {
    loading.value = true;
    form.post(route("admin.login.store"), {
        onFinish: () => (loading.value = false),
    });
};
</script>

<template>
    <div
        class="min-h-screen flex items-center justify-center p-4 bg-gradient relative overflow-hidden"
    >
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div
                class="absolute top-1/4 -left-10 w-72 h-72 bg-purple-500/30 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute bottom-1/4 right-0 w-96 h-96 bg-emerald-500/30 rounded-full blur-3xl"
            ></div>
            <div
                class="absolute top-1/3 right-1/4 w-64 h-64 bg-blue-500/30 rounded-full blur-3xl"
            ></div>
        </div>

        <div class="w-full max-w-md md:max-w-[380px] relative">
            <!-- Основная карточка -->
            <div
                class="bg-white/10 backdrop-blur-lg rounded-3xl overflow-hidden shadow-xl border border-white/20 transition-all duration-300"
            >
                <!-- Кнопка "назад" с анимацией -->
                <button
                    class="absolute top-6 left-6 p-2 rounded-full hover:bg-white/10 transition-colors duration-200"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-gray-300"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M15 19l-7-7 7-7"
                        />
                    </svg>
                </button>

                <!-- Основной контент -->
                <div class="px-8 pt-20 pb-8">
                    <h1 class="text-3xl font-bold mb-2 text-white">
                        Вход в систему
                    </h1>
                    <p class="text-gray-300 mb-6">
                        Введите свои учетные данные для входа
                    </p>

                    <!-- Вывод общей ошибки -->
                    <div
                        v-if="form.errors.default"
                        class="mb-6 p-4 bg-red-500/10 backdrop-blur-sm border border-red-500/20 rounded-xl relative overflow-hidden group"
                    >
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-red-500/10 to-transparent group-hover:opacity-75 transition-opacity"
                        ></div>
                        <div class="relative flex items-center space-x-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-red-400"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                            <p class="text-red-300 text-sm">
                                Произошла ошибка при входе
                            </p>
                        </div>
                    </div>

                    <!-- Форма -->
                    <form @submit.prevent="submit" class="space-y-4">
                        <!-- Поле для пользователя с аватаром -->
                        <div class="space-y-2">
                            <label class="text-sm text-gray-300 ml-1"
                                >Имя пользователя</label
                            >
                            <div
                                :class="[
                                    'flex items-center space-x-3 bg-white/5 border rounded-xl p-3 transition duration-200',
                                    form.errors.username
                                        ? 'border-red-500/50'
                                        : 'border-white/10 focus-within:border-emerald-500/50',
                                ]"
                            >
                                <div
                                    class="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-emerald-400"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <input
                                    v-model="form.username"
                                    type="text"
                                    class="flex-1 bg-transparent border-none placeholder-gray-500 focus:ring-0 text-gray-200"
                                    placeholder="Введите имя пользователя"
                                />
                            </div>
                            <!-- Ошибка username -->
                            <div
                                v-if="form.errors.username"
                                class="flex items-center space-x-2 ml-1"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-red-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                                <p class="text-red-400 text-sm">
                                    {{ form.errors.username }}
                                </p>
                            </div>
                        </div>

                        <!-- Поле для пароля -->
                        <div class="space-y-2">
                            <label class="text-sm text-gray-300 ml-1"
                                >Пароль</label
                            >
                            <div class="relative">
                                <div
                                    :class="[
                                        'flex items-center bg-white/5 border rounded-xl p-3 transition duration-200',
                                        form.errors.password
                                            ? 'border-red-500/50'
                                            : 'border-white/10 focus-within:border-emerald-500/50',
                                    ]"
                                >
                                    <input
                                        v-model="form.password"
                                        :type="
                                            showPassword ? 'text' : 'password'
                                        "
                                        class="flex-1 bg-transparent border-none placeholder-gray-500 focus:ring-0 text-gray-200"
                                        placeholder="Введите пароль"
                                    />
                                    <button
                                        type="button"
                                        @click="togglePassword"
                                        class="text-emerald-400 hover:text-emerald-300 transition-colors px-2 text-sm focus:outline-none"
                                    >
                                        {{
                                            showPassword ? "Скрыть" : "Показать"
                                        }}
                                    </button>
                                </div>
                                <!-- Ошибка password -->
                                <div
                                    v-if="form.errors.password"
                                    class="flex items-center space-x-2 mt-1 ml-1"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 text-red-400"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <p class="text-red-400 text-sm">
                                        {{ form.errors.password }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Кнопка входа -->
                        <button
                            type="submit"
                            :disabled="loading"
                            class="w-full bg-emerald-500 hover:bg-emerald-400 text-gray-900 font-medium rounded-xl py-3.5 transition duration-200 mt-8 relative overflow-hidden group"
                        >
                            <span
                                :class="{ 'opacity-0': loading }"
                                class="transition-opacity"
                            >
                                Войти
                            </span>
                            <span
                                v-if="loading"
                                class="absolute inset-0 flex items-center justify-center"
                            >
                                <svg
                                    class="animate-spin h-5 w-5 text-gray-900"
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
                                    ></circle>
                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.backdrop-blur-lg {
    backdrop-filter: blur(20px);
}

.bg-gradient {
    background: linear-gradient(125deg, #1a1a1a 0%, #2d2d2d 100%);
}

/* Добавляем плавную анимацию для фокуса полей */
input:focus::placeholder {
    opacity: 0.5;
    transition: opacity 0.2s;
}

/* Улучшенная анимация для кнопки при наведении */
button[type="submit"]:not(:disabled):hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

@keyframes float {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
    100% {
        transform: translateY(0px);
    }
}

.absolute {
    animation: float 15s ease-in-out infinite;
}

.absolute:nth-child(2) {
    animation-delay: -5s;
}

.absolute:nth-child(3) {
    animation-delay: -10s;
}
</style>
    
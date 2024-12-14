<script setup>
import { reactive, ref, watch } from "vue";
import FormInput from "../../Components/FormInput.vue";
import {
    Mail,
    Lock,
    MessageSquare,
    Phone,
    UserPlus,
    ShieldCheck,
    Zap,
    ArrowLeft,
    BadgeCheck,
    Wallet,
} from "lucide-vue-next";
import { router, usePage, Link } from "@inertiajs/vue3";

defineProps({
    errors: {
        type: Object,
    },
});

const page = usePage();
const companyName = "Payment System";
const loading = ref(false);
const passwordStrength = ref(0);

const form = reactive({
    email: "",
    password: "",
    password_confirmation: "",
    telegram: "",
    phone: "",
    agreement: false,
});

watch(() => form.password, calculatePasswordStrength);

function calculatePasswordStrength(password) {
    let score = 0;
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    passwordStrength.value = score;
}

const getStrengthColor = () => {
    const colors = [
        "bg-red-500",
        "bg-orange-500",
        "bg-yellow-500",
        "bg-green-500",
    ];
    return colors[passwordStrength.value - 1] || "bg-gray-200";
};

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
    <div class="min-h-screen bg-gray-50 flex">
        <!-- Left Panel - Hero Section -->
        <div
            class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-to-br from-indigo-600 to-purple-700"
        >
            <div
                class="absolute inset-0 bg-black opacity-10 pattern-grid"
            ></div>
            <div class="relative w-full flex flex-col p-12 text-white">
                <!-- Logo/Brand -->
                <div class="flex items-center space-x-3 mb-12">
                    <Wallet class="w-8 h-8" />
                    <span class="text-2xl font-bold">{{ companyName }}</span>
                </div>

                <!-- Hero Content -->
                <div class="my-auto">
                    <h1 class="text-5xl font-bold mb-6">
                        Управляйте криптовалютой
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-amber-300 to-pink-300"
                        >
                            безопасно и легко
                        </span>
                    </h1>
                    <p class="text-xl text-gray-200 mb-12">
                        Создайте свой кошелек за несколько секунд и начните
                        работу с криптовалютой прямо сейчас
                    </p>

                    <!-- Features -->
                    <div class="grid grid-cols-2 gap-8 mt-12">
                        <div class="flex items-start space-x-4">
                            <ShieldCheck
                                class="w-6 h-6 text-amber-300 shrink-0"
                            />
                            <div>
                                <h3 class="font-semibold">Безопасность</h3>
                                <p class="text-sm text-gray-300">
                                    Многоуровневая защита средств
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <Zap class="w-6 h-6 text-amber-300 shrink-0" />
                            <div>
                                <h3 class="font-semibold">Быстрые переводы</h3>
                                <p class="text-sm text-gray-300">
                                    Мгновенные транзакции
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-4">
                            <BadgeCheck
                                class="w-6 h-6 text-amber-300 shrink-0"
                            />
                            <div>
                                <h3 class="font-semibold">Верификация</h3>
                                <p class="text-sm text-gray-300">
                                    Простая процедура KYC
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trust Badge -->
                <div class="mt-auto">
                    <div class="flex items-center space-x-2">
                        <div class="flex">
                            <div
                                v-for="i in 5"
                                :key="i"
                                class="text-amber-300 w-5 h-5"
                            >
                                ★
                            </div>
                        </div>
                        <span class="text-sm ml-2"
                            >4.9/5 рейтинг пользователей</span
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div
                    class="lg:hidden flex items-center justify-center space-x-3 mb-8"
                >
                    <Wallet class="w-8 h-8 text-indigo-600" />
                    <span class="text-2xl font-bold text-gray-900">{{
                        companyName
                    }}</span>
                </div>

                <!-- Form Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <div class="mb-8 text-center">
                        <h2 class="text-2xl font-bold text-gray-900">
                            Создать аккаунт
                        </h2>
                        <p class="text-gray-600 mt-2">
                            Заполните форму для регистрации
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <FormInput
                            v-model="form.email"
                            title="Email"
                            type="email"
                            :message="errors.email"
                            :icon="Mail"
                            placeholder="Введите email"
                            autocomplete="email"
                        />

                        <FormInput
                            v-model="form.telegram"
                            title="Telegram"
                            type="text"
                            :message="errors.telegram"
                            :icon="MessageSquare"
                            placeholder="@username"
                        />

                        <FormInput
                            v-model="form.phone"
                            title="Телефон"
                            type="tel"
                            :message="errors.phone"
                            :icon="Phone"
                            placeholder="+7 (999) 999-99-99"
                        />

                        <div class="space-y-2">
                            <FormInput
                                v-model="form.password"
                                title="Пароль"
                                type="password"
                                :message="errors.password"
                                :icon="Lock"
                                placeholder="Минимум 8 символов"
                                :minLength="8"
                                autocomplete="new-password"
                            />
                            <!-- Password Strength Indicator -->
                            <div
                                class="h-1 w-full bg-gray-200 rounded-full overflow-hidden"
                            >
                                <div
                                    class="h-full transition-all duration-300"
                                    :class="getStrengthColor()"
                                    :style="{
                                        width: `${passwordStrength * 25}%`,
                                    }"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-500">
                                Минимум 8 символов, заглавные буквы, цифры и
                                спецсимволы
                            </p>
                        </div>

                        <FormInput
                            v-model="form.password_confirmation"
                            title="Подтверждение пароля"
                            type="password"
                            :message="errors.password_confirmation"
                            :icon="Lock"
                            placeholder="Повторите пароль"
                            :minLength="8"
                            autocomplete="new-password"
                        />

                        <div class="flex items-start space-x-3">
                            <input
                                v-model="form.agreement"
                                type="checkbox"
                                class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <p class="text-sm text-gray-600">
                                Я согласен с
                                <a
                                    href="#"
                                    class="text-indigo-600 hover:text-indigo-700 font-medium"
                                >
                                    Условиями использования
                                </a>
                                и
                                <a
                                    href="#"
                                    class="text-indigo-600 hover:text-indigo-700 font-medium"
                                >
                                    Политикой конфиденциальности
                                </a>
                            </p>
                        </div>

                        <button
                            :disabled="!form.agreement || loading"
                            type="submit"
                            class="w-full flex justify-center items-center space-x-2 py-3 px-4 rounded-xl text-white font-semibold bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                        >
                            <span>Создать аккаунт</span>
                            <UserPlus v-if="!loading" class="w-5 h-5" />
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

                        <div class="text-center space-y-4">
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center justify-center space-x-2 text-indigo-600 hover:text-indigo-700 font-medium"
                            >
                                <ArrowLeft class="w-4 h-4" />
                                <span>Уже есть аккаунт? Войти</span>
                            </Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.pattern-grid {
    background-image: radial-gradient(
        rgba(255, 255, 255, 0.1) 1px,
        transparent 1px
    );
    background-size: 20px 20px;
}
</style>

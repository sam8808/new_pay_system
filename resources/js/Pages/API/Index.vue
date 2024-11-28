<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useForm } from "@inertiajs/vue3";

const isLoading = ref(false);
const isSubmitting = ref(false);

// Определение props
const props = defineProps({
    shop: {
        type: Object,
        required: true,
    },
    data: {
        type: Object,
        required: true,
    },
    paymentSystems: {
        type: Array,
        required: true,
    },
});

// Иконки безопасности
const securityItems = [
    {
        path: "M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z",
        text: "SSL защита",
    },
    {
        path: "M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z",
        text: "3D-Secure",
    },
    {
        path: "M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2",
        text: "PCI DSS",
    },
];

// Список гарантий безопасности
const securityGuarantees = [
    {
        title: "PCI DSS Сертификация",
        description:
            "Соответствие международным стандартам безопасности платежей",
    },
    {
        title: "3-D Secure 2.0",
        description: "Дополнительный уровень защиты для всех транзакций",
    },
    {
        title: "Антифрод система",
        description: "Мониторинг и предотвращение мошеннических операций",
    },
];

// Платежные карты
const paymentCards = ["visa", "mastercard", "mir"];

// Форма
const form = useForm({
    payment_system: "",
    _token: document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content"),
});

// Таймер
const timeLeft = ref("14:59");
let timer;

// Методы
const startTimer = (duration) => {
    let time = duration;
    timer = setInterval(() => {
        const minutes = Math.floor(time / 60);
        const seconds = time % 60;

        timeLeft.value = `${String(minutes).padStart(2, "0")}:${String(
            seconds
        ).padStart(2, "0")}`;

        if (--time < 0) {
            clearInterval(timer);
            timeLeft.value = "00:00";
        }
    }, 1000);
};

const submit = async () => {
    if (isSubmitting.value) return;

    isSubmitting.value = true;
    try {
        await form.post(route("api.pay"), {
            preserveScroll: true,
            onSuccess: () => {
                // Можно добавить успешное сообщение
            },
            onError: () => {
                // Можно добавить обработку ошибок
            },
        });
    } finally {
        isSubmitting.value = false;
    }
};

// Добавляем computed свойство для проверки валидности формы
const isFormValid = computed(() => {
    return !!form.payment_system;
});

// Добавляем метод для форматирования суммы
const formatAmount = (amount) => {
    return new Intl.NumberFormat("ru-RU", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
};

// Lifecycle hooks
onMounted(() => {
    startTimer(14 * 60 + 59); // 14 минут 59 секунд
});

onUnmounted(() => {
    if (timer) {
        clearInterval(timer);
    }
});
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Верхняя секция с градиентом -->
        <div
            class="bg-gradient-to-br from-slate-950 via-slate-900 to-slate-800 text-white py-8 lg:py-12 relative overflow-hidden"
        >
            <!-- Фоновый паттерн -->
            <div class="absolute inset-0">
                <svg
                    class="absolute inset-0 w-full h-full opacity-20"
                    viewBox="0 0 30 30"
                    xmlns="http://www.w3.org/2000/svg"
                >
                    <defs>
                        <pattern
                            id="dot-pattern"
                            x="0"
                            y="0"
                            width="30"
                            height="30"
                            patternUnits="userSpaceOnUse"
                        >
                            <path
                                d="M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z"
                                fill="rgba(255,255,255,0.05)"
                            />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#dot-pattern)" />
                </svg>
            </div>

            <!-- Основной контент хедера -->
            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8"
                >
                    <!-- Левая часть хедера -->
                    <div class="relative">
                        <!-- Платежные системы и статус -->
                        <div class="flex flex-wrap items-center gap-4 mb-6">
                            <!-- Карточки платежных систем -->
                            <div class="flex -space-x-2">
                                <div
                                    v-for="card in paymentCards"
                                    :key="card"
                                    class="w-14 h-9 relative bg-white/10 backdrop-blur-sm rounded-xl border border-white/10 flex items-center justify-center transform hover:-translate-y-0.5 transition-all duration-300"
                                >
                                    <img
                                        :src="`/images/cards/${card}.svg`"
                                        :alt="card"
                                        class="h-5"
                                    />
                                </div>
                            </div>

                            <!-- Разделитель -->
                            <div class="h-8 w-px bg-white/20"></div>

                            <!-- Статус системы -->
                            <div
                                class="flex items-center px-4 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20"
                            >
                                <span
                                    class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse mr-2"
                                ></span>
                                <span
                                    class="text-emerald-400 text-sm font-medium"
                                    >Система активна</span
                                >
                            </div>
                        </div>

                        <!-- Заголовок -->
                        <h1
                            class="text-4xl lg:text-5xl font-light mb-6 bg-clip-text text-transparent bg-gradient-to-r from-white to-white/80"
                        >
                            Безопасный платеж
                        </h1>

                        <!-- Иконки безопасности -->
                        <div class="flex flex-wrap items-center gap-6">
                            <div
                                v-for="(item, index) in securityItems"
                                :key="index"
                                class="flex items-center gap-2"
                            >
                                <svg
                                    class="w-5 h-5 text-emerald-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        :d="item.path"
                                    />
                                </svg>
                                <span class="text-slate-300">{{
                                    item.text
                                }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Правая часть хедера -->
                    <div class="flex items-center gap-6">
                        <div
                            class="hidden lg:block h-16 w-px bg-gradient-to-b from-transparent via-slate-700/50 to-transparent"
                        ></div>
                        <div class="grid grid-cols-2 gap-4">
                            <div
                                class="flex flex-col items-center p-4 rounded-xl bg-white/5 backdrop-blur-lg border border-white/10"
                            >
                                <div class="text-emerald-400 font-mono text-lg">
                                    256-bit
                                </div>
                                <div class="text-xs text-slate-400">
                                    Шифрование
                                </div>
                            </div>
                            <div
                                class="flex flex-col items-center p-4 rounded-xl bg-white/5 backdrop-blur-lg border border-white/10"
                            >
                                <div class="text-emerald-400 font-mono text-lg">
                                    99.9%
                                </div>
                                <div class="text-xs text-slate-400">Uptime</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Основной контент -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-16">
            <!-- Информация о магазине -->
            <div class="relative -mt-16 sm:-mt-20 mb-12">
                <div
                    class="inline-flex flex-wrap sm:flex-nowrap items-center gap-6 bg-white/95 backdrop-blur-2xl rounded-2xl p-6 sm:p-8 shadow-[0_8px_32px_rgba(0,0,0,0.12)] border border-white/20"
                >
                    <!-- Логотип магазина -->
                    <div
                        class="w-16 h-16 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 p-0.5 shrink-0 shadow-lg"
                    >
                        <div
                            class="w-full h-full rounded-lg bg-white flex items-center justify-center p-3 transition-all duration-300 hover:scale-105"
                        >
                            <img
                                :src="shop.logo"
                                :alt="shop.title"
                                class="max-w-full max-h-full object-contain"
                            />
                        </div>
                    </div>

                    <!-- Информация о магазине -->
                    <div
                        class="flex flex-wrap sm:flex-nowrap items-center gap-6 sm:divide-x divide-slate-200"
                    >
                        <div class="space-y-1">
                            <div class="text-sm text-slate-500">
                                Вам выставлен счет от
                            </div>
                            <div class="text-xl text-emerald-600 font-medium">
                                {{ shop.title }}
                            </div>
                        </div>

                        <!-- Верификация -->
                        <div class="pl-0 sm:pl-6 flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center shadow-emerald-200/50 shadow-lg"
                            >
                                <svg
                                    class="w-5 h-5 text-emerald-600"
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
                            </div>
                            <div>
                                <div
                                    class="text-sm font-medium text-emerald-700"
                                >
                                    Верифицированный продавец
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ shop.base_url }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Сетка с информацией о платеже и выбором способа оплаты -->
            <div class="grid lg:grid-cols-12 gap-8">
                <!-- Левая колонка -->
                <div class="lg:col-span-8 space-y-8">
                    <!-- Информация о платеже -->
                    <div class="bg-white rounded-3xl shadow-xl p-8">
                        <div class="flex justify-between items-center mb-8">
                            <h2 class="text-2xl font-light text-slate-800">
                                Информация о платеже
                            </h2>
                            <div
                                class="w-2 h-2 bg-emerald-400 rounded-full animate-pulse"
                            ></div>
                        </div>

                        <!-- Детали платежа -->
                        <dl class="space-y-6">
                            <div
                                class="flex items-center justify-between py-4 border-b border-slate-100"
                            >
                                <dt class="text-slate-500">ID транзакции</dt>
                                <dd class="font-mono text-lg text-slate-800">
                                    {{ data.order }}
                                </dd>
                            </div>
                            <div
                                class="flex items-center justify-between py-4 border-b border-slate-100"
                            >
                                <dt class="text-slate-500">Получатель</dt>
                                <dd class="text-lg text-slate-800">
                                    {{ shop.base_url }}
                                </dd>
                            </div>
                            <div class="flex items-center justify-between py-4">
                                <dt class="text-slate-500">Сумма к оплате</dt>
                                <dd class="flex items-baseline gap-2">
                                    <span
                                        class="text-4xl font-light text-slate-900"
                                        >{{ data.amount }}</span
                                    >
                                    <span class="text-sm text-slate-500">{{
                                        data.currency
                                    }}</span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Форма выбора способа оплаты -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Заголовок секции -->
                        <div class="flex justify-between items-center">
                            <h3 class="text-2xl font-light text-slate-800">
                                Способ оплаты
                            </h3>
                            <div class="flex -space-x-2">
                                <div
                                    v-for="card in paymentCards"
                                    :key="card"
                                    class="w-14 h-9 bg-white rounded-xl shadow-md flex items-center justify-center hover:-translate-y-0.5 transition-transform"
                                >
                                    <img
                                        :src="`/images/cards/${card}.svg`"
                                        :alt="card"
                                        class="h-4"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Сетка платежных систем -->
                        <div class="grid sm:grid-cols-3 gap-4">
                            <label
                                v-for="system in paymentSystems"
                                :key="system.id"
                                class="relative block cursor-pointer group/card"
                            >
                                <input
                                    type="radio"
                                    v-model="form.payment_system"
                                    :value="system.id"
                                    class="peer sr-only"
                                />
                                <div
                                    class="relative bg-white p-4 rounded-xl border-2 border-slate-200 transition-all duration-300 ease-out hover:border-slate-300 hover:shadow-lg peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 peer-checked:shadow-emerald-500/10 peer-checked:shadow-lg group-hover/card:translate-y-[-2px]"
                                >
                                    <div class="flex items-start gap-3">
                                        <!-- Улучшенный логотип платежной системы -->
                                        <div
                                            class="shrink-0 w-10 h-10 rounded-lg bg-slate-50 p-2 transition-transform duration-300 ease-out group-hover/card:scale-110"
                                        >
                                            <img
                                                :src="system.logo"
                                                :alt="system.title"
                                                class="w-full h-full object-contain"
                                            />
                                        </div>

                                        <!-- Улучшенная информация о платежной системе -->
                                        <div class="flex-1 min-w-0">
                                            <div
                                                class="flex items-center justify-between mb-1"
                                            >
                                                <h4
                                                    class="text-sm font-medium text-slate-900 truncate transition-colors duration-300 group-hover/card:text-emerald-600"
                                                >
                                                    {{ system.title }}
                                                </h4>
                                                <!-- Улучшенный индикатор выбора -->
                                                <div class="relative w-5 h-5">
                                                    <div
                                                        class="absolute inset-0 rounded-full border-2 transition-all duration-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-500 group-hover/card:border-emerald-400"
                                                    ></div>
                                                    <svg
                                                        class="absolute inset-0 w-5 h-5 text-white scale-0 transition-transform duration-300 peer-checked:scale-100"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                </div>
                                            </div>
                                            <p
                                                class="text-xs text-slate-500 truncate"
                                            >
                                                {{ system.description }}
                                            </p>

                                            <!-- Добавляем бейджики преимуществ -->
                                            <div class="flex gap-2 mt-2">
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800"
                                                >
                                                    <svg
                                                        class="w-3 h-3 mr-1"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                    Быстро
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                                >
                                                    <svg
                                                        class="w-3 h-3 mr-1"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fill-rule="evenodd"
                                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                            clip-rule="evenodd"
                                                        />
                                                    </svg>
                                                    Безопасно
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <!-- Сообщение об ошибке -->
                        <div
                            v-if="form.errors.payment_system"
                            class="rounded-lg bg-red-50 border border-red-100 p-4"
                        >
                            <div class="flex items-center text-sm text-red-600">
                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                {{ form.errors.payment_system }}
                            </div>
                        </div>

                        <!-- Кнопка оплаты -->
                        <div class="pt-6">
                            <button
                                type="submit"
                                :disabled="!isFormValid || isSubmitting"
                                class="relative w-full bg-gradient-to-r from-slate-800 to-slate-900 text-white px-8 py-4 rounded-xl text-lg font-medium shadow-xl hover:shadow-2xl hover:-translate-y-0.5 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-slate-900/20 disabled:opacity-70 disabled:cursor-not-allowed overflow-hidden"
                            >
                                <div
                                    class="relative z-10 flex items-center justify-center gap-3"
                                >
                                    <svg
                                        v-if="isSubmitting"
                                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
                                    <span class="truncate">
                                        {{
                                            isSubmitting
                                                ? "Обработка платежа..."
                                                : `Оплатить ${formatAmount(
                                                      data.amount
                                                  )} ${data.currency}`
                                        }}
                                    </span>
                                    <svg
                                        v-if="!isSubmitting"
                                        class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M14 5l7 7m0 0l-7 7m7-7H3"
                                        />
                                    </svg>
                                </div>
                                <!-- Добавляем эффект пульсации при наведении -->
                                <div
                                    class="absolute inset-0 rounded-xl transition-opacity duration-300 bg-gradient-to-r from-emerald-500/20 to-emerald-500/0 opacity-0 group-hover:opacity-100"
                                ></div>
                            </button>

                            <!-- Информация о безопасности -->
                            <div
                                class="flex items-center justify-center mt-4 text-xs text-slate-500"
                            >
                                <svg
                                    class="w-4 h-4 mr-1.5 text-emerald-500"
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
                                Безопасная оплата с шифрованием данных
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Правая колонка -->
                <div class="lg:col-span-4">
                    <div class="sticky top-8 space-y-6">
                        <!-- Блок безопасности -->
                        <div
                            class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-3xl p-8 shadow-xl relative overflow-hidden group"
                        >
                            <!-- Фоновый паттерн такой же как в шапке -->
                            <div class="absolute inset-0">
                                <svg
                                    class="absolute inset-0 w-full h-full opacity-20"
                                    viewBox="0 0 30 30"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <defs>
                                        <pattern
                                            id="dot-pattern-2"
                                            x="0"
                                            y="0"
                                            width="30"
                                            height="30"
                                            patternUnits="userSpaceOnUse"
                                        >
                                            <path
                                                d="M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z"
                                                fill="rgba(255,255,255,0.05)"
                                            />
                                        </pattern>
                                    </defs>
                                    <rect
                                        width="100%"
                                        height="100%"
                                        fill="url(#dot-pattern-2)"
                                    />
                                </svg>
                            </div>

                            <!-- Индикатор состояния -->
                            <div class="absolute -top-4 -right-4">
                                <div
                                    class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"
                                ></div>
                            </div>

                            <h3 class="text-2xl font-light mb-8 relative">
                                Гарантии безопасности
                            </h3>

                            <!-- Список гарантий -->
                            <div class="space-y-6">
                                <div
                                    v-for="(
                                        security, index
                                    ) in securityGuarantees"
                                    :key="index"
                                    class="group/item flex items-start gap-4 transition-all duration-300 hover:translate-x-2"
                                >
                                    <div
                                        class="flex-shrink-0 w-12 h-12 rounded-xl bg-white/10 backdrop-blur-sm border border-white/5 shadow-lg flex items-center justify-center group-hover/item:bg-white/15 transition-all duration-300"
                                    >
                                        <svg
                                            class="w-6 h-6 text-emerald-400 transition-transform duration-300 group-hover/item:scale-110"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="1.5"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4
                                            class="text-base font-medium text-white transition-colors duration-300 group-hover/item:text-emerald-400"
                                        >
                                            {{ security.title }}
                                        </h4>
                                        <p
                                            class="mt-2 text-sm text-slate-400 leading-relaxed transition-colors duration-300 group-hover/item:text-slate-300"
                                        >
                                            {{ security.description }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Таймер сессии -->
                                <div
                                    class="pt-6 mt-6 border-t border-slate-700/50"
                                >
                                    <div
                                        class="flex items-center justify-between px-4 py-3 bg-slate-800/50 backdrop-blur-xl rounded-xl border border-white/5"
                                    >
                                        <span class="text-sm text-slate-400"
                                            >Время сессии:</span
                                        >
                                        <span
                                            class="font-mono text-lg text-emerald-400 tabular-nums tracking-wider"
                                            >{{ timeLeft }}</span
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Блок поддержки -->
                        <div
                            class="bg-white/90 backdrop-blur-xl rounded-xl p-6 shadow-lg border border-white/20"
                        >
                            <div class="flex items-center gap-3 mb-4">
                                <svg
                                    class="w-5 h-5 text-slate-400"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"
                                    />
                                </svg>
                                <span class="text-sm font-medium text-slate-600"
                                    >Поддержка 24/7</span
                                >
                            </div>
                            <p class="text-xs text-slate-500">
                                Если у вас возникли вопросы, наша служба
                                поддержки всегда готова помочь
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: .8;
    transform: scale(0.98);
  }
}

.animate-slide-in {
  animation: slideIn 0.3s ease-out forwards;
}

.group-hover\:pulse:hover {
  animation: pulse 2s infinite;
}

/* Улучшаем стили для мобильных устройств */
@media (max-width: 640px) {
  .sm\:grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
  }
  
  .sm\:p-4 {
    padding: 1rem;
  }
  
  .sm\:text-sm {
    font-size: 0.875rem;
    line-height: 1.25rem;
  }
}

/* Улучшаем эффекты наведения */
.hover\:scale-up {
  transition: transform 0.2s ease-out;
}

.hover\:scale-up:hover {
  transform: scale(1.02);
}
</style>

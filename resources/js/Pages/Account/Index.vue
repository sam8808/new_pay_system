```vue
<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import AccountLayout from "@/Layouts/AccountLayout.vue";
import {
    Wallet,
    ArrowUpRight,
    ArrowDownRight,
    Store,
    Activity,
    Clock,
    ChevronRight,
    ShieldCheck,
    Zap,
    Building2,
    CircleDollarSign,
    ArrowRightLeft,
    Calendar,
    Users,
} from "lucide-vue-next";

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    recentTransactions: {
        type: Array,
        required: true,
    },
    monthlyStats: {
        type: Object,
        required: true,
    },
    wallets: {
        type: Array,
        required: true,
    },
});

// Проверка существования маршрута
const hasRoute = (name) => {
    try {
        route(name);
        return true;
    } catch {
        return false;
    }
};

// Определение типа валюты
const isCrypto = (currency) => {
    const cryptoCurrencies = ["BTC", "ETH", "USDT"];
    return cryptoCurrencies.includes(currency);
};

// Форматирование суммы в зависимости от типа валюты
const formatAmount = (amount, currency) => {
    const value = Number(amount);

    if (isCrypto(currency)) {
        if (value < 0.0001) return value.toFixed(8);
        if (value < 0.01) return value.toFixed(6);
        if (value < 1) return value.toFixed(4);
        return value.toFixed(2);
    }

    return value.toFixed(2);
};

// Форматирование валюты
const formatCurrency = (amount, currency = "USD") => {
    const value = formatAmount(amount, currency);

    if (isCrypto(currency)) {
        return `${value} ${currency}`;
    }

    try {
        return new Intl.NumberFormat("ru-RU", {
            style: "currency",
            currency: currency,
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(amount);
    } catch {
        return `${value} ${currency}`;
    }
};

// Форматирование даты
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

// Определение статуса транзакции
const getTransactionStatus = (status) => {
    const statusMap = {
        completed: {
            class: "bg-emerald-50 text-emerald-700",
            text: "Выполнено",
        },
        pending: {
            class: "bg-amber-50 text-amber-700",
            text: "В обработке",
        },
        failed: {
            class: "bg-red-50 text-red-700",
            text: "Ошибка",
        },
        processing: {
            class: "bg-blue-50 text-blue-700",
            text: "Обработка",
        },
        canceled: {
            class: "bg-gray-50 text-gray-700",
            text: "Отменено",
        },
    };
    return statusMap[status] || statusMap.pending;
};

// Определение типа транзакции
const getTransactionType = (type) => {
    const typeMap = {
        deposit: {
            icon: ArrowUpRight,
            class: "text-emerald-600 bg-emerald-50",
            prefix: "+",
        },
        withdrawal: {
            icon: ArrowDownRight,
            class: "text-red-600 bg-red-50",
            prefix: "-",
        },
        transfer: {
            icon: ArrowRightLeft,
            class: "text-blue-600 bg-blue-50",
            prefix: "",
        },
        exchange: {
            icon: CircleDollarSign,
            class: "text-violet-600 bg-violet-50",
            prefix: "≈",
        },
    };
    return typeMap[type] || typeMap.deposit;
};
</script>

<template>
    <AccountLayout>
        <div class="min-h-screen bg-gray-50/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Кошельки -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">
                            Мои кошельки
                        </h2>
                    </div>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6"
                    >
                        <div
                            v-for="wallet in wallets"
                            :key="wallet.id"
                            class="bg-white rounded-2xl p-6 shadow-lg shadow-gray-100/50 hover:shadow-xl transition-all duration-300 border border-gray-100/50"
                        >
                            <!-- Хедер кошелька -->
                            <div class="flex items-center justify-between mb-4">
                                <div
                                    class="w-12 h-12 rounded-2xl flex items-center justify-center"
                                    :class="`bg-${wallet.currency.code.toLowerCase()}-50`"
                                >
                                    <Wallet
                                        class="w-6 h-6"
                                        :class="`text-${wallet.currency.code.toLowerCase()}-600`"
                                    />
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-sm font-semibold"
                                        :class="`text-${wallet.currency.code.toLowerCase()}-600 bg-${wallet.currency.code.toLowerCase()}-50`"
                                    >
                                        {{ wallet.currency.code }}
                                    </span>
                                    <div
                                        v-if="wallet.is_default"
                                        class="px-2 py-1 bg-violet-50 text-violet-600 rounded-lg text-xs font-medium"
                                    >
                                        По умолчанию
                                    </div>
                                </div>
                            </div>

                            <!-- Балансы -->
                            <div class="space-y-2">
                                <div>
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Доступно
                                    </p>
                                    <p class="text-2xl font-bold text-gray-900">
                                        {{
                                            formatCurrency(
                                                wallet.balance,
                                                wallet.currency.code
                                            )
                                        }}
                                    </p>
                                </div>
                                <div v-if="wallet.reserved_balance > 0">
                                    <p
                                        class="text-sm font-medium text-gray-500"
                                    >
                                        Зарезервировано
                                    </p>
                                    <p
                                        class="text-lg font-semibold text-gray-600"
                                    >
                                        {{
                                            formatCurrency(
                                                wallet.reserved_balance,
                                                wallet.currency.code
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Основные метрики -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
                >
                    <!-- Количество магазинов -->
                    <div
                        class="bg-white rounded-2xl p-6 shadow-lg shadow-gray-100/50 hover:shadow-xl transition-all duration-300 border border-gray-100/50"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center"
                            >
                                <Store class="w-6 h-6 text-indigo-600" />
                            </div>
                            <span
                                class="px-2.5 py-1 rounded-lg text-sm font-medium text-indigo-600 bg-indigo-50"
                                >Всего</span
                            >
                        </div>
                        <p class="text-sm font-medium text-gray-500">
                            Активные мерчанты
                        </p>
                        <p class="text-2xl font-bold text-gray-900 mt-2">
                            {{ stats.merchantsCount }}
                        </p>
                    </div>

                    <!-- Поступления за сегодня -->
                    <div
                        class="bg-white rounded-2xl p-6 shadow-lg shadow-gray-100/50 hover:shadow-xl transition-all duration-300 border border-gray-100/50"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center"
                            >
                                <ArrowUpRight
                                    class="w-6 h-6 text-emerald-600"
                                />
                            </div>
                            <div class="flex items-center space-x-2">
                                <Clock class="w-5 h-5 text-emerald-600" />
                                <span class="text-sm text-emerald-600"
                                    >Сегодня</span
                                >
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-500">
                            Поступления
                        </p>
                        <p class="text-2xl font-bold text-emerald-600 mt-2">
                            {{ formatCurrency(stats.todayIncome) }}
                        </p>
                    </div>

                    <!-- Выводы за сегодня -->
                    <div
                        class="bg-white rounded-2xl p-6 shadow-lg shadow-gray-100/50 hover:shadow-xl transition-all duration-300 border border-gray-100/50"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center"
                            >
                                <ArrowDownRight class="w-6 h-6 text-red-600" />
                            </div>
                            <div class="flex items-center space-x-2">
                                <Clock class="w-5 h-5 text-red-600" />
                                <span class="text-sm text-red-600"
                                    >Сегодня</span
                                >
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-500">Выводы</p>
                        <p class="text-2xl font-bold text-red-600 mt-2">
                            {{ formatCurrency(stats.todayWithdrawals) }}
                        </p>
                    </div>

                    <!-- Объем за месяц -->
                    <div
                        class="bg-white rounded-2xl p-6 shadow-lg shadow-gray-100/50 hover:shadow-xl transition-all duration-300 border border-gray-100/50"
                    >
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 bg-violet-50 rounded-2xl flex items-center justify-center"
                            >
                                <Activity class="w-6 h-6 text-violet-600" />
                            </div>
                            <div class="flex items-center space-x-2">
                                <Calendar class="w-5 h-5 text-violet-600" />
                                <span class="text-sm text-violet-600"
                                    >За месяц</span
                                >
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-500">
                            Общий объем
                        </p>
                        <p class="text-2xl font-bold text-violet-600 mt-2">
                            {{ formatCurrency(monthlyStats.monthlyVolume) }}
                        </p>
                    </div>
                </div>

                <!-- Последние операции и Статистика -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Последние операции -->
                    <div class="lg:col-span-2">
                        <div
                            class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden border border-gray-100/50"
                        >
                            <!-- Заголовок -->
                            <div class="px-6 py-4 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-bold text-gray-900">
                                        Последние операции
                                    </h2>
                                    <Link
                                        v-if="hasRoute('transactions')"
                                        :href="route('transactions')"
                                        class="text-sm text-violet-600 hover:text-violet-700 font-medium flex items-center"
                                    >
                                        Все операции
                                        <ChevronRight class="w-4 h-4 ml-1" />
                                    </Link>
                                </div>
                            </div>

                            <!-- Список транзакций -->
                            <div class="divide-y divide-gray-100">
                                <div
                                    v-for="transaction in recentTransactions"
                                    :key="transaction.id"
                                    class="px-6 py-4 hover:bg-gray-50/50 transition-colors"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div
                                            class="flex items-center space-x-4"
                                        >
                                            <div
                                                class="w-10 h-10 rounded-xl flex items-center justify-center"
                                                :class="
                                                    getTransactionType(
                                                        transaction.type
                                                    ).class
                                                "
                                            >
                                                <component
                                                    :is="
                                                        getTransactionType(
                                                            transaction.type
                                                        ).icon
                                                    "
                                                    class="w-5 h-5"
                                                />
                                            </div>

                                            <div>
                                                <p
                                                    class="text-sm font-semibold text-gray-900"
                                                >
                                                    {{
                                                        transaction.type ===
                                                        "deposit"
                                                            ? "Пополнение"
                                                            : transaction.type ===
                                                              "withdrawal"
                                                            ? "Вывод"
                                                            : transaction.type ===
                                                              "transfer"
                                                            ? "Перевод"
                                                            : "Обмен"
                                                    }}
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500"
                                                >
                                                    {{
                                                        formatDate(
                                                            transaction.created_at
                                                        )
                                                    }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="text-right">
                                            <p
                                                class="text-sm font-semibold"
                                                :class="
                                                    getTransactionType(
                                                        transaction.type
                                                    ).class
                                                "
                                            >
                                                {{
                                                    getTransactionType(
                                                        transaction.type
                                                    ).prefix
                                                }}
                                                {{
                                                    formatCurrency(
                                                        transaction.amount,
                                                        transaction.currency
                                                            .code
                                                    )
                                                }}
                                            </p>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :class="
                                                    getTransactionStatus(
                                                        transaction.status
                                                    ).class
                                                "
                                            >
                                                {{
                                                    getTransactionStatus(
                                                        transaction.status
                                                    ).text
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Пустое состояние -->
                                <div
                                    v-if="recentTransactions.length === 0"
                                    class="px-6 py-8 text-center"
                                >
                                    <Activity
                                        class="w-12 h-12 text-gray-400 mx-auto mb-3"
                                    />
                                    <h3
                                        class="text-sm font-medium text-gray-900 mb-1"
                                    >
                                        Нет транзакций
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        История транзакций пока пуста
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Боковая статистика -->
                    <div class="space-y-6">
                        <!-- Быстрые действия -->
                        <div
                            class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden border border-gray-100/50"
                        >
                            <div class="px-6 py-4 border-b border-gray-100">
                                <h2 class="text-xl font-bold text-gray-900">
                                    Быстрые действия
                                </h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <Link
                                    v-if="hasRoute('merchant.create')"
                                    :href="route('merchant.create')"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 focus:ring-4 focus:ring-violet-500/20 transition-all duration-300"
                                >
                                    <Store class="w-4 h-4 mr-2" />
                                    Новый мерчант
                                </Link>

                                <Link
                                    v-if="hasRoute('withdrawal.create')"
                                    :href="route('withdrawal.create')"
                                    class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-300"
                                >
                                    <Wallet class="w-4 h-4 mr-2" />
                                    Вывод средств
                                </Link>
                            </div>
                        </div>

                        <!-- Статистика за месяц -->
                        <div
                            class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden border border-gray-100/50"
                        >
                            <div class="px-6 py-4 border-b border-gray-100">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-bold text-gray-900">
                                        Статистика за
                                        {{
                                            new Date().toLocaleString("ru", {
                                                month: "long",
                                            })
                                        }}
                                    </h2>
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-violet-50 text-violet-600"
                                    >
                                        {{ monthlyStats.totalTransactions }}
                                        операций
                                    </span>
                                </div>
                            </div>

                            <div class="p-6 space-y-6">
                                <!-- Успешные платежи -->
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span
                                            class="text-sm font-medium text-gray-500"
                                            >Успешные платежи</span
                                        >
                                        <span
                                            class="text-sm font-bold text-gray-900"
                                            >{{
                                                monthlyStats.successRate
                                            }}%</span
                                        >
                                    </div>
                                    <div
                                        class="h-2 bg-gray-100 rounded-full overflow-hidden"
                                    >
                                        <div
                                            class="h-full bg-gradient-to-r from-violet-500 to-violet-600 rounded-full transition-all duration-300"
                                            :style="{
                                                width: `${monthlyStats.successRate}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>

                                <!-- Активные мерчанты -->
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span
                                            class="text-sm font-medium text-gray-500"
                                            >Активные мерчанты</span
                                        >
                                        <span
                                            class="text-sm font-bold text-gray-900"
                                        >
                                            {{
                                                monthlyStats.merchantsStats
                                                    .active
                                            }}
                                            из
                                            {{
                                                monthlyStats.merchantsStats
                                                    .total
                                            }}
                                        </span>
                                    </div>
                                    <div
                                        class="h-2 bg-gray-100 rounded-full overflow-hidden"
                                    >
                                        <div
                                            class="h-full bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-full transition-all duration-300"
                                            :style="{
                                                width: `${
                                                    (monthlyStats.merchantsStats
                                                        .active /
                                                        monthlyStats
                                                            .merchantsStats
                                                            .total) *
                                                    100
                                                }%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>

                                <!-- Средний чек -->
                                <div class="pt-4 border-t border-gray-100">
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span
                                            class="text-sm font-medium text-gray-500"
                                            >Средний чек</span
                                        >
                                        <span
                                            class="text-sm font-bold text-gray-900"
                                        >
                                            {{
                                                formatCurrency(
                                                    monthlyStats.averagePayment
                                                )
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Общий объем -->
                                <div class="pt-4 border-t border-gray-100">
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span
                                            class="text-sm font-medium text-gray-500"
                                            >Общий объем</span
                                        >
                                        <span
                                            class="text-sm font-bold text-gray-900"
                                        >
                                            {{
                                                formatCurrency(
                                                    monthlyStats.monthlyVolume
                                                )
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AccountLayout>
</template>

<style scoped>
.card-hover-effect {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-hover-effect:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
        0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.progress-bar-animation {
    transition: width 1s ease-in-out;
}
</style>

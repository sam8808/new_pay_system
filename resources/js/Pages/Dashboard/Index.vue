<script setup>
import { ref } from "vue";
import DashboardLayout from "@/Layouts/DashboardLayout.vue";
import { Link } from "@inertiajs/vue3";
import {
    Building,
    ArrowUpRight,
    ArrowDownRight,
    Wallet,
    Users,
    Store,
    BarChart3,
    Activity,
    Clock,
    ChevronRight,
    Calendar,
} from "lucide-vue-next";

// Входящие данные из контроллера
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
});

// Форматирование валюты
const formatCurrency = (amount) => {
    return new Intl.NumberFormat("ru-RU", {
        style: "currency",
        currency: "RUB",
        minimumFractionDigits: 2,
    }).format(amount);
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
const getTransactionStatus = (transaction) => {
    if (transaction.confirmed) {
        return {
            class: "bg-emerald-50 text-emerald-700",
            text: "Подтверждено",
        };
    }
    if (transaction.canceled) {
        return {
            class: "bg-red-50 text-red-700",
            text: "Отменено",
        };
    }
    return {
        class: "bg-amber-50 text-amber-700",
        text: "В обработке",
    };
};

// Определение типа транзакции и его отображения
const getTransactionType = (type) => {
    return (
        {
            payment: {
                icon: ArrowUpRight,
                class: "text-emerald-600 bg-emerald-50",
                prefix: "+",
            },
            withdrawal: {
                icon: ArrowDownRight,
                class: "text-red-600 bg-red-50",
                prefix: "-",
            },
        }[type] || {
            icon: ArrowUpRight,
            class: "text-gray-600 bg-gray-50",
            prefix: "",
        }
    );
};
</script>

<template>
    <DashboardLayout>
        <div class="container mx-auto px-6 py-8">
            <!-- Основные метрики -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
            >
                <!-- Общий баланс -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-12 h-12 bg-violet-50 rounded-xl flex items-center justify-center"
                        >
                            <Wallet class="w-6 h-6 text-violet-600" />
                        </div>
                        <Calendar class="w-5 h-5 text-gray-400" />
                    </div>
                    <p class="text-sm font-medium text-gray-500">
                        Общий баланс
                    </p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        {{ formatCurrency(stats.totalBalance) }}
                    </p>
                </div>

                <!-- Количество магазинов -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center"
                        >
                            <Store class="w-6 h-6 text-emerald-600" />
                        </div>
                        <Activity class="w-5 h-5 text-gray-400" />
                    </div>
                    <p class="text-sm font-medium text-gray-500">Магазины</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        {{ stats.merchantsCount }}
                    </p>
                </div>

                <!-- Поступления за сегодня -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center"
                        >
                            <ArrowUpRight class="w-6 h-6 text-green-600" />
                        </div>
                        <Clock class="w-5 h-5 text-gray-400" />
                    </div>
                    <p class="text-sm font-medium text-gray-500">
                        Поступления сегодня
                    </p>
                    <p class="text-2xl font-semibold text-green-600 mt-1">
                        {{ formatCurrency(stats.todayIncome) }}
                    </p>
                </div>

                <!-- Выводы за сегодня -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center"
                        >
                            <ArrowDownRight class="w-6 h-6 text-red-600" />
                        </div>
                        <Clock class="w-5 h-5 text-gray-400" />
                    </div>
                    <p class="text-sm font-medium text-gray-500">
                        Выводы сегодня
                    </p>
                    <p class="text-2xl font-semibold text-red-600 mt-1">
                        {{ formatCurrency(stats.todayWithdrawals) }}
                    </p>
                </div>
            </div>

            <!-- Последние операции и графики -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Последние операции -->
                <div
                    class="lg:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden"
                >
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h2 class="font-medium text-gray-900">
                                Последние операции
                            </h2>
                            <Link
                                :href="route('transactions')"
                                class="text-sm text-violet-600 hover:text-violet-700 font-medium"
                            >
                                Все операции
                            </Link>
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <div
                            v-for="transaction in recentTransactions"
                            :key="transaction.id"
                            class="px-6 py-4 hover:bg-gray-50/50 transition-colors"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <!-- Иконка типа транзакции -->
                                    <div
                                        class="w-10 h-10 rounded-xl flex items-center justify-center"
                                        :class="
                                            getTransactionType(transaction.type)
                                                .class
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

                                    <!-- Информация о транзакции -->
                                    <div>
                                        <p
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{
                                                transaction.payment_id
                                                    ? `Платёж #${transaction.payment_id}`
                                                    : transaction.withdrawal_id
                                                    ? `Вывод #${transaction.withdrawal_id}`
                                                    : `Транзакция #${transaction.id}`
                                            }}
                                        </p>
                                        <div
                                            class="flex items-center gap-2 text-xs text-gray-500"
                                        >
                                            <span>{{
                                                formatDate(
                                                    transaction.created_at
                                                )
                                            }}</span>
                                            <span>•</span>
                                            <span
                                                >ID:
                                                {{ transaction.m_id }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Сумма и статус -->
                                <div class="text-right">
                                    <p
                                        class="text-sm font-medium"
                                        :class="{
                                            'text-emerald-600':
                                                transaction.type === 'payment',
                                            'text-red-600':
                                                transaction.type ===
                                                'withdrawal',
                                        }"
                                    >
                                        {{
                                            getTransactionType(transaction.type)
                                                .prefix
                                        }}
                                        {{ formatCurrency(transaction.amount) }}
                                        <span
                                            class="text-xs text-gray-500 ml-1"
                                            >{{ transaction.currency }}</span
                                        >
                                    </p>
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                        :class="
                                            getTransactionStatus(transaction)
                                                .class
                                        "
                                    >
                                        {{
                                            getTransactionStatus(transaction)
                                                .text
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
                            <h3 class="text-sm font-medium text-gray-900 mb-1">
                                Нет транзакций
                            </h3>
                            <p class="text-sm text-gray-500">
                                История транзакций пока пуста
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Боковая панель -->
                <div class="space-y-6">
                    <!-- Быстрые действия -->
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h2 class="font-medium text-gray-900">
                                Быстрые действия
                            </h2>
                        </div>
                        <div class="p-6 space-y-4">
                            <Link
                                :href="route('merchant.create')"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 transition-colors"
                            >
                                <Store class="w-4 h-4 mr-2" />
                                Подключить магазин
                            </Link>

                            <Link
                                :href="route('withdrawal')"
                                class="w-full inline-flex items-center justify-center px-4 py-2.5 border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors"
                            >
                                <Wallet class="w-4 h-4 mr-2" />
                                Вывести средства
                            </Link>
                        </div>
                    </div>

                    <!-- Статистика -->
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <h2 class="font-medium text-gray-900">
                                    Статистика за
                                    {{
                                        new Date().toLocaleString("ru", {
                                            month: "long",
                                        })
                                    }}
                                </h2>
                                <span class="text-xs text-gray-500">
                                    {{ monthlyStats.totalTransactions }}
                                    транзакций
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="space-y-4">
                                <!-- Успешные платежи -->
                                <div class="space-y-2">
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span class="text-sm text-gray-500"
                                            >Успешные платежи</span
                                        >
                                        <span
                                            class="text-sm font-medium text-gray-900"
                                        >
                                            {{ monthlyStats.successRate }}%
                                        </span>
                                    </div>
                                    <!-- Прогресс бар -->
                                    <div
                                        class="h-2 bg-gray-100 rounded-full overflow-hidden"
                                    >
                                        <div
                                            class="h-full bg-violet-500 rounded-full"
                                            :style="{
                                                width: `${monthlyStats.successRate}%`,
                                            }"
                                        ></div>
                                    </div>
                                </div>

                                <!-- Средний чек -->
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500"
                                        >Средний чек</span
                                    >
                                    <span
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{
                                            formatCurrency(
                                                monthlyStats.averagePayment
                                            )
                                        }}
                                    </span>
                                </div>

                                <!-- Активные магазины -->
                                <div class="space-y-2">
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span class="text-sm text-gray-500"
                                            >Активные магазины</span
                                        >
                                        <span
                                            class="text-sm font-medium text-gray-900"
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
                                    <!-- Прогресс бар -->
                                    <div
                                        class="h-2 bg-gray-100 rounded-full overflow-hidden"
                                    >
                                        <div
                                            class="h-full bg-emerald-500 rounded-full"
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

                                <!-- Объем платежей -->
                                <div class="pt-2 mt-2 border-t border-gray-100">
                                    <div
                                        class="flex justify-between items-center"
                                    >
                                        <span class="text-sm text-gray-500"
                                            >Объем платежей</span
                                        >
                                        <span
                                            class="text-sm font-medium text-gray-900"
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
    </DashboardLayout>
</template>

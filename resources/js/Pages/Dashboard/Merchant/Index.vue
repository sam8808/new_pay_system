<script setup>
import { ref } from "vue";
import Pagination from "@/Components/Pagination.vue";

import {
    Store,
    ShieldCheck,
    AlertCircle,
    Ban,
    Clock,
    Check,
    XCircle,
    Plus,
} from "lucide-vue-next";

const props = defineProps({
    merchants: {
        type: Object,
        required: true,
    },
});

// Форматирование валюты
const formatCurrency = (amount, currency) => {
    return (
        new Intl.NumberFormat("ru-RU", {
            style: "decimal",
            minimumFractionDigits: 2,
        }).format(amount) +
        " " +
        currency
    );
};

// Получение статуса магазина
const getMerchantStatus = (merchant) => {
    if (merchant.banned) {
        return {
            icon: Ban,
            class: "bg-red-50 text-red-700",
            text: "Заблокирован",
        };
    }
    if (merchant.rejected) {
        return {
            icon: XCircle,
            class: "bg-red-50 text-red-700",
            text: "Отказано",
        };
    }
    if (!merchant.activated) {
        return {
            icon: AlertCircle,
            class: "bg-yellow-50 text-yellow-700",
            text: "Отключен",
        };
    }
    return {
        icon: Check,
        class: "bg-emerald-50 text-emerald-700",
        text: "Подключен",
    };
};

// Получение статуса модерации
const getModerationStatus = (merchant) => {
    if (merchant.approved && !merchant.rejected) {
        return {
            icon: ShieldCheck,
            class: "bg-emerald-50 text-emerald-700",
            text: "Подтвержден",
        };
    }
    return {
        icon: Clock,
        class: "bg-yellow-50 text-yellow-700",
        text: "На модерации",
    };
};
</script>

<template>
    <DashboardLayout>
        <div class="container mx-auto px-6 py-8">
            <!-- Заголовок и кнопка -->
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4"
            >
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-medium text-gray-900">
                        Список магазинов
                    </h1>
                    <span class="text-sm text-gray-500"
                        >Всего: {{ merchants.total }}</span
                    >
                </div>

                <Link
                    :href="route('merchant.create')"
                    class="inline-flex items-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 transition-colors"
                >
                    <Plus class="w-4 h-4 mr-2" />
                    Подключить магазин
                </Link>
            </div>

            <!-- Таблица -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table v-if="merchants.data.length > 0" class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    ID
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    Название
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    Баланс
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    Комиссия
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    Статус
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    Модерация
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="merchant in merchants.data"
                                :key="merchant.id"
                                class="hover:bg-gray-50/50 transition-colors"
                            >
                                <!-- ID -->
                                <td class="px-6 py-4">
                                    <Link
                                        :href="
                                            route('merchant.show', merchant.id)
                                        "
                                        class="text-sm font-medium text-violet-600 hover:text-violet-700"
                                    >
                                        {{ merchant.m_id }}
                                    </Link>
                                </td>

                                <!-- Название -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <Store class="w-5 h-5 text-gray-400" />
                                        <span
                                            class="text-sm font-medium text-gray-900"
                                            >{{ merchant.title }}</span
                                        >
                                    </div>
                                </td>

                                <!-- Баланс -->
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{
                                            formatCurrency(
                                                merchant.balance,
                                                "RUB"
                                            )
                                        }}
                                    </span>
                                </td>

                                <!-- Комиссия -->
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                                    >
                                        {{ merchant.percent }}%
                                    </span>
                                </td>

                                <!-- Статус -->
                                <td class="px-6 py-4">
                                    <div
                                        :class="[
                                            'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium gap-1.5',
                                            getMerchantStatus(merchant).class,
                                        ]"
                                    >
                                        <component
                                            :is="
                                                getMerchantStatus(merchant).icon
                                            "
                                            class="w-4 h-4"
                                        />
                                        {{ getMerchantStatus(merchant).text }}
                                    </div>
                                </td>

                                <!-- Модерация -->
                                <td class="px-6 py-4">
                                    <div
                                        :class="[
                                            'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium gap-1.5',
                                            getModerationStatus(merchant).class,
                                        ]"
                                    >
                                        <component
                                            :is="
                                                getModerationStatus(merchant)
                                                    .icon
                                            "
                                            class="w-4 h-4"
                                        />
                                        {{ getModerationStatus(merchant).text }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Пустое состояние -->
                    <div
                        v-else
                        class="flex flex-col items-center justify-center py-12 px-4"
                    >
                        <Store class="w-12 h-12 text-gray-400 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-1">
                            Нет магазинов
                        </h3>
                        <p class="text-sm text-gray-500 mb-4">
                            У вас нет подключенных магазинов на данный момент
                        </p>
                        <Link
                            :href="route('merchant.create')"
                            class="inline-flex items-center px-4 py-2 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 transition-colors"
                        >
                            <Plus class="w-4 h-4 mr-2" />
                            Подключить первый магазин
                        </Link>
                    </div>
                </div>

                <!-- Пагинация -->
                <div
                    v-if="merchants.data.length > 0"
                    class="border-t border-gray-100 px-4 py-3"
                >
                    <pagination :links="merchants.links" />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
/* Тонкий скроллбар */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}
</style>

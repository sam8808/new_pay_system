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

// Получение статуса магазина
const getMerchantStatus = (merchant) => {
    if (merchant.is_active) {
        return {
            icon: Check,
            class: "bg-emerald-50 text-emerald-700",
            text: "Подключен",
        };
    }
    return {
        icon: AlertCircle,
        class: "bg-yellow-50 text-yellow-700",
        text: "Отключен",
    };
};

// Получение статуса модерации
const getModerationStatus = (merchant) => {
    if (merchant.is_succes_moderation) {
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
    <AccountLayout>
        <div class="container mx-auto px-8 py-8">
            <!-- Заголовок и кнопка -->
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4"
            >
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Список магазинов
                    </h1>
                    <span
                        class="px-2.5 py-1 text-sm font-medium bg-violet-50 text-violet-700 rounded-lg"
                    >
                        Всего: {{ merchants.total }}
                    </span>
                </div>

                <Link
                    :href="route('merchant.create')"
                    class="inline-flex items-center px-5 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 shadow-lg shadow-violet-600/20 transition-all duration-300"
                >
                    <Plus class="w-4 h-4 mr-2" />
                    Подключить магазин
                </Link>
            </div>

            <!-- Таблица -->
            <div
                class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden backdrop-blur-xl"
            >
                <div class="overflow-x-auto">
                    <table v-if="merchants.data.length > 0" class="w-full">
                        <thead>
                            <tr>
                                <th
                                    class="px-8 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80"
                                >
                                    ID
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80"
                                >
                                    Название
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80"
                                >
                                    Комиссия
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80"
                                >
                                    Статус
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80"
                                >
                                    Модерация
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80"
                                >
                                    Дата создании
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="merchant in merchants.data"
                                :key="merchant.merchant_id"
                                class="hover:bg-gray-50/50 transition-all duration-300"
                            >
                                <!-- ID -->
                                <td class="px-8 py-4">
                                    <Link
                                        :href="
                                            route('merchant.show', merchant.id)
                                        "
                                        class="text-sm font-semibold text-violet-600 hover:text-violet-700 transition-colors"
                                    >
                                        {{ merchant.merchant_id }}
                                    </Link>
                                </td>

                                <!-- Название -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center"
                                        >
                                            <Store
                                                class="w-4 h-4 text-gray-400"
                                            />
                                        </div>
                                        <span
                                            class="text-sm font-medium text-gray-900"
                                            >{{ merchant.title }}</span
                                        >
                                    </div>
                                </td>

                                <!-- Комиссия -->
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-violet-50 text-violet-700"
                                    >
                                        {{ merchant.processing_fee }}%
                                    </span>
                                </td>

                                <!-- Статус -->
                                <td class="px-6 py-4">
                                    <div
                                        :class="[
                                            'inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium gap-1.5',
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
                                            'inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium gap-1.5',
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

                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-violet-50 text-violet-700"
                                    >
                                        {{ merchant.created_at }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Пустое состояние -->
                    <div
                        v-else
                        class="flex flex-col items-center justify-center py-16 px-4"
                    >
                        <div
                            class="w-16 h-16 rounded-2xl bg-violet-50 flex items-center justify-center mb-4"
                        >
                            <Store class="w-8 h-8 text-violet-600" />
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            Нет магазинов
                        </h3>
                        <p class="text-sm text-gray-500 mb-6">
                            У вас нет подключенных магазинов на данный момент
                        </p>
                        <Link
                            :href="route('merchant.create')"
                            class="inline-flex items-center px-5 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 shadow-lg shadow-violet-600/20 transition-all duration-300"
                        >
                            <Plus class="w-4 h-4 mr-2" />
                            Подключить первый магазин
                        </Link>
                    </div>
                </div>

                <!-- Пагинация -->
                <div
                    v-if="merchants.data.length > 0"
                    class="border-t border-gray-100 px-6 py-4 bg-gray-50/80"
                >
                    <pagination :links="merchants.links" />
                </div>
            </div>
        </div>
    </AccountLayout>
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
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}

/* Улучшенные эффекты */
.shadow-lg {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hover\:shadow-xl:hover {
    transform: translateY(-2px);
}

/* Эффект размытия */
.backdrop-blur-xl {
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
}
</style>

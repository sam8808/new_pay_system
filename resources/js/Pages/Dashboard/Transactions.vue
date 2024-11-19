<script setup>
import { ref, computed } from "vue";
import Pagination from "@/Components/Pagination.vue";
import {
    CheckCircle,
    XCircle,
    AlertCircle,
    Search,
    Download,
    Filter,
    ArrowUpDown,
    Eye,
} from "lucide-vue-next";

// Пропсы
const props = defineProps({
    transactions: {
        type: Object,
        required: true,
    },
});

// Состояния
const searchQuery = ref("");
const selectedStatus = ref("all");
const selectedType = ref("all");
const sortDirection = ref("desc");
const showDetails = ref(null);
const selectedRows = ref(new Set());

// Фильтрация операций
const filteredTransaction = computed(() => {
    return props.transactions.data.filter((transaction) => {
        const matchesSearch =
            transaction.id.toString().includes(searchQuery.value) ||
            searchQuery.value === "";

        const matchesStatus =
            selectedStatus.value === "all"
                ? true
                : selectedStatus.value === "confirmed"
                ? transaction.confirmed
                : selectedStatus.value === "canceled"
                ? transaction.canceled
                : true;

        const matchesType =
            selectedType.value === "all"
                ? true
                : transaction.type === selectedType.value;

        return matchesSearch && matchesStatus && matchesType;
    });
});

// Форматирование даты
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat("ru-RU", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    }).format(date);
};

// Форматирование суммы
const formatAmount = (amount) => {
    return new Intl.NumberFormat("ru-RU", {
        style: "currency",
        currency: "RUB",
        minimumFractionDigits: 2,
    }).format(amount);
};

// Получение статуса операции
const getTransactionStatus = (transaction) => {
    if (transaction.confirmed) {
        return {
            icon: CheckCircle,
            class: "bg-green-100 text-green-700",
            text: "Подтверждено",
        };
    }
    if (transaction.canceled) {
        return {
            icon: XCircle,
            class: "bg-red-100 text-red-700",
            text: "Отменено",
        };
    }
    return {
        icon: AlertCircle,
        class: "bg-yellow-100 text-yellow-700",
        text: "В обработке",
    };
};

// Переключение выбора строки
const toggleRowSelection = (id) => {
    if (selectedRows.value.has(id)) {
        selectedRows.value.delete(id);
    } else {
        selectedRows.value.add(id);
    }
};
</script>

<template>
    <DashboardLayout>
        <div class="container mx-auto px-6 py-8">
            <!-- Заголовок и действия -->
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4"
            >
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-medium text-gray-900">
                        История операций
                    </h1>
                    <!-- <span class="text-sm text-gray-500">Последнее обновление 12 мин назад</span> -->
                </div>

                <div class="flex items-center gap-3">
                    <button
                        class="inline-flex items-center px-3 py-2 rounded-xl bg-violet-600 text-white text-sm font-medium hover:bg-violet-700 transition-colors"
                    >
                        <Filter class="w-4 h-4 mr-2" />
                        Фильтры
                    </button>
                </div>
            </div>

            <!-- Панель фильтров -->
            <div class="bg-white rounded-2xl shadow-sm mb-6">
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-4"
                >
                    <!-- Поиск -->
                    <div class="relative">
                        <Search
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                        />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Поиск по ID..."
                            class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-violet-500"
                        />
                    </div>

                    <!-- Фильтры -->
                    <select
                        v-model="selectedStatus"
                        class="w-full px-4 py-2.5 text-sm bg-gray-50 border-0 rounded-xl appearance-none focus:ring-2 focus:ring-violet-500"
                    >
                        <option value="all">Все статусы</option>
                        <option value="confirmed">Подтверждено</option>
                        <option value="canceled">Отменено</option>
                        <option value="pending">В обработке</option>
                    </select>

                    <select
                        v-model="selectedType"
                        class="w-full px-4 py-2.5 text-sm bg-gray-50 border-0 rounded-xl appearance-none focus:ring-2 focus:ring-violet-500"
                    >
                        <option value="all">Все типы</option>
                        <option value="payIn">Пополнения</option>
                        <option value="payOut">Выводы</option>
                    </select>

                    <button
                        @click="
                            sortDirection =
                                sortDirection === 'asc' ? 'desc' : 'asc'
                        "
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm text-gray-700 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors"
                    >
                        <ArrowUpDown class="w-4 h-4" />
                        {{
                            sortDirection === "asc"
                                ? "Сначала старые"
                                : "Сначала новые"
                        }}
                    </button>
                </div>
            </div>

            <!-- Таблица -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    ID
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    Дата
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    Тип
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    Сумма
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    Валюта
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    Статус
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50"
                                >
                                    Действия
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="transaction in filteredTransaction"
                                :key="transaction.id"
                                class="hover:bg-gray-50/50 transition-colors"
                            >
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-medium text-gray-900"
                                        >#{{ transaction.id }}</span
                                    >
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{
                                        formatDate(transaction.created_at)
                                    }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium',
                                            transaction.type === 'payIn'
                                                ? 'bg-emerald-50 text-emerald-700'
                                                : 'bg-red-50 text-red-700',
                                        ]"
                                    >
                                        {{
                                            transaction.type === "payIn"
                                                ? "Пополнение"
                                                : "Вывод"
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="[
                                            'text-sm font-medium',
                                            transaction.type === 'payIn'
                                                ? 'text-emerald-600'
                                                : 'text-red-600',
                                        ]"
                                    >
                                        {{ formatAmount(transaction.amount) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{
                                        transaction.currency
                                    }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        :class="[
                                            'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium gap-1.5',
                                            getTransactionStatus(transaction)
                                                .class,
                                        ]"
                                    >
                                        <component
                                            :is="
                                                getTransactionStatus(
                                                    transaction
                                                ).icon
                                            "
                                            class="w-4 h-4"
                                        />
                                        {{
                                            getTransactionStatus(transaction)
                                                .text
                                        }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <button
                                        @click="
                                            showDetails =
                                                showDetails === transaction.id
                                                    ? null
                                                    : transaction.id
                                        "
                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
                                    >
                                        <Eye class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="border-t border-gray-100 bg-white">
                    <Pagination :links="transactions.links" class="px-4" />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
/* Стилизация селектов */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

/* Убираем стандартную стрелку в Safari */
select::-webkit-scrollbar {
    display: none;
}

/* Стили для активных состояний */
.hover\:bg-gray-50:hover {
    background-color: rgba(249, 250, 251, 0.5);
}

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

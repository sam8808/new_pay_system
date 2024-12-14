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
    <AccountLayout>
        <div class="container mx-auto px-8 py-8">
            <!-- Заголовок и действия -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-semibold text-gray-900">История операций</h1>
                </div>

                <div class="flex items-center gap-3">
                    <button class="inline-flex items-center px-4 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-medium hover:bg-violet-700 shadow-lg shadow-violet-600/20 transition-all duration-300">
                        <Filter class="w-4 h-4 mr-2" />
                        Фильтры
                    </button>
                </div>
            </div>

            <!-- Панель фильтров -->
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 mb-6 backdrop-blur-xl">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-6">
                    <!-- Поиск -->
                    <div class="relative">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
                        <input v-model="searchQuery"
                               type="text"
                               placeholder="Поиск по ID..."
                               class="w-full pl-11 pr-4 py-3 text-sm bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-violet-500 transition-shadow duration-300" />
                    </div>

                    <!-- Фильтры -->
                    <select v-model="selectedStatus"
                            class="w-full px-4 py-3 text-sm bg-gray-50 border-0 rounded-xl appearance-none focus:ring-2 focus:ring-violet-500 transition-shadow duration-300">
                        <option value="all">Все статусы</option>
                        <option value="confirmed">Подтверждено</option>
                        <option value="canceled">Отменено</option>
                        <option value="pending">В обработке</option>
                    </select>

                    <select v-model="selectedType"
                            class="w-full px-4 py-3 text-sm bg-gray-50 border-0 rounded-xl appearance-none focus:ring-2 focus:ring-violet-500 transition-shadow duration-300">
                        <option value="all">Все типы</option>
                        <option value="payIn">Пополнения</option>
                        <option value="payOut">Выводы</option>
                    </select>

                    <button @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
                            class="inline-flex items-center justify-center gap-2 px-4 py-3 text-sm text-gray-700 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300">
                        <ArrowUpDown class="w-4 h-4" />
                        {{ sortDirection === "asc" ? "Сначала старые" : "Сначала новые" }}
                    </button>
                </div>
            </div>

            <!-- Таблица -->
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden backdrop-blur-xl">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80">ID</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80">Дата</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80">Тип</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80">Сумма</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80">Валюта</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80">Статус</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80">Действия</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="transaction in filteredTransaction"
                                :key="transaction.id"
                                class="hover:bg-gray-50/50 transition-all duration-300">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-900">#{{ transaction.id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{ formatDate(transaction.created_at) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="[
                                        'inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium',
                                        transaction.type === 'payIn'
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-red-50 text-red-700',
                                    ]">
                                        {{ transaction.type === "payIn" ? "Пополнение" : "Вывод" }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span :class="[
                                        'text-sm font-semibold',
                                        transaction.type === 'payIn'
                                            ? 'text-emerald-600'
                                            : 'text-red-600',
                                    ]">
                                        {{ formatAmount(transaction.amount) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">{{ transaction.currency }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div :class="[
                                        'inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium gap-1.5',
                                        getTransactionStatus(transaction).class,
                                    ]">
                                        <component :is="getTransactionStatus(transaction).icon"
                                                   class="w-4 h-4" />
                                        {{ getTransactionStatus(transaction).text }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <button @click="showDetails = showDetails === transaction.id ? null : transaction.id"
                                            class="p-2.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-300">
                                        <Eye class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="border-t border-gray-100 bg-white/80 backdrop-blur-xl">
                    <Pagination :links="transactions.links" class="px-6 py-1" />
                </div>
            </div>
        </div>
    </AccountLayout>
</template>

<style scoped>
/* Стилизация селектов */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 1rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.75rem;
}

/* Убираем стандартную стрелку в Safari */
select::-webkit-scrollbar {
    display: none;
}

/* Улучшенные эффекты наведения */
.hover\:bg-gray-50:hover {
    background-color: rgba(249, 250, 251, 0.7);
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
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}

/* Улучшенные эффекты перехода */
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Эффект размытия */
.backdrop-blur-xl {
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
}
</style>
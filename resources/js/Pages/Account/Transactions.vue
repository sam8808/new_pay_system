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

const props = defineProps({
    transactions: {
        type: Object,
        required: true,
    },
});

const searchQuery = ref("");
const selectedStatus = ref("all");
const selectedType = ref("all");
const sortDirection = ref("desc");
const showDetails = ref(null);
const selectedRows = ref(new Set());

// Статусы и типы транзакций
const STATUSES = {
    pending: "В обработке",
    completed: "Подтверждено",
    failed: "Неудача",
    canceled: "Отменено",
};

const TYPES = {
    deposit: "Пополнение",
    withdrawal: "Вывод",
    transfer: "Перевод",
    exchange: "Обмен",
    fee: "Комиссия",
    referral: "Реферальные",
    refund: "Возврат",
};

const filteredTransaction = computed(() => {
    return props.transactions.data.filter((transaction) => {
        const matchesSearch =
            transaction.uuid
                .toLowerCase()
                .includes(searchQuery.value.toLowerCase()) ||
            searchQuery.value === "";

        const matchesStatus =
            selectedStatus.value === "all" ||
            transaction.status === selectedStatus.value;

        const matchesType =
            selectedType.value === "all" ||
            transaction.type === selectedType.value;

        return matchesSearch && matchesStatus && matchesType;
    });
});

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

const formatAmount = (amount, currency) => {
    return (
        new Intl.NumberFormat("ru-RU", {
            style: "decimal",
            minimumFractionDigits: 2,
            maximumFractionDigits: 8,
        }).format(amount) + ` ${currency}`
    );
};

const getTransactionStatus = (transaction) => {
    switch (transaction.status) {
        case "completed":
            return {
                icon: CheckCircle,
                class: "bg-green-100 text-green-700",
                text: STATUSES[transaction.status],
            };
        case "failed":
        case "canceled":
            return {
                icon: XCircle,
                class: "bg-red-100 text-red-700",
                text: STATUSES[transaction.status],
            };
        default:
            return {
                icon: AlertCircle,
                class: "bg-yellow-100 text-yellow-700",
                text: STATUSES[transaction.status],
            };
    }
};

const getTransactionTypeStyle = (type) => {
    const styles = {
        deposit: "bg-emerald-50 text-emerald-700",
        withdrawal: "bg-red-50 text-red-700",
        transfer: "bg-blue-50 text-blue-700",
        exchange: "bg-purple-50 text-purple-700",
        fee: "bg-gray-50 text-gray-700",
        referral: "bg-amber-50 text-amber-700",
        refund: "bg-pink-50 text-pink-700",
    };
    return styles[type] || "bg-gray-50 text-gray-700";
};
</script>

<template>
    <AccountLayout>
        <div class="container mx-auto px-8 py-8">
            <!-- Заголовок и действия -->
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4"
            >
                <div class="flex items-center gap-4">
                    <h1 class="text-2xl font-semibold text-gray-900">
                        История транзакций
                    </h1>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        class="inline-flex items-center px-4 py-2.5 rounded-xl bg-violet-600 text-white text-sm font-medium hover:bg-violet-700 shadow-lg shadow-violet-600/20 transition-all duration-300"
                    >
                        <Filter class="w-4 h-4 mr-2" />
                        Фильтры
                    </button>
                </div>
            </div>

            <!-- Панель фильтров -->
            <div
                class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 mb-6 backdrop-blur-xl"
            >
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 p-6"
                >
                    <!-- Поиск -->
                    <div class="relative">
                        <Search
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                        />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Поиск по UUID..."
                            class="w-full pl-11 pr-4 py-3 text-sm bg-gray-50 border-0 rounded-xl focus:ring-2 focus:ring-violet-500 transition-shadow duration-300"
                        />
                    </div>

                    <!-- Фильтр статуса -->
                    <select
                        v-model="selectedStatus"
                        class="w-full px-4 py-3 text-sm bg-gray-50 border-0 rounded-xl appearance-none focus:ring-2 focus:ring-violet-500 transition-shadow duration-300"
                    >
                        <option value="all">Все статусы</option>
                        <option
                            v-for="(label, status) in STATUSES"
                            :key="status"
                            :value="status"
                        >
                            {{ label }}
                        </option>
                    </select>

                    <!-- Фильтр типа -->
                    <select
                        v-model="selectedType"
                        class="w-full px-4 py-3 text-sm bg-gray-50 border-0 rounded-xl appearance-none focus:ring-2 focus:ring-violet-500 transition-shadow duration-300"
                    >
                        <option value="all">Все типы</option>
                        <option
                            v-for="(label, type) in TYPES"
                            :key="type"
                            :value="type"
                        >
                            {{ label }}
                        </option>
                    </select>

                    <!-- Сортировка -->
                    <button
                        @click="
                            sortDirection =
                                sortDirection === 'asc' ? 'desc' : 'asc'
                        "
                        class="inline-flex items-center justify-center gap-2 px-4 py-3 text-sm text-gray-700 bg-gray-50 rounded-xl hover:bg-gray-100 transition-all duration-300"
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
            <div
                class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden backdrop-blur-xl"
            >
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80"
                                >
                                    UUID
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80"
                                >
                                    Дата
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80"
                                >
                                    Тип
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/80"
                                >
                                    Сумма
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
                                    Действия
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            <tr
                                v-for="transaction in filteredTransaction"
                                :key="transaction.uuid"
                                class="hover:bg-gray-50/50 transition-all duration-300"
                            >
                                <td class="px-6 py-4">
                                    <span
                                        class="text-sm font-medium text-gray-900"
                                        >{{ transaction.uuid }}</span
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
                                            'inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium',
                                            getTransactionTypeStyle(
                                                transaction.type
                                            ),
                                        ]"
                                    >
                                        {{ TYPES[transaction.type] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        :class="[
                                            'text-sm font-semibold',
                                            transaction.type === 'deposit'
                                                ? 'text-emerald-600'
                                                : 'text-red-600',
                                        ]"
                                    >
                                        {{
                                            formatAmount(
                                                transaction.amount,
                                                transaction.currency?.code
                                            )
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">
                                        {{
                                            formatAmount(
                                                transaction.fee,
                                                transaction.currency?.code
                                            )
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div
                                        :class="[
                                            'inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium gap-1.5',
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
                                                showDetails === transaction.uuid
                                                    ? null
                                                    : transaction.uuid
                                        "
                                        class="p-2.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-all duration-300"
                                    >
                                        <Eye class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div
                    class="border-t border-gray-100 bg-white/80 backdrop-blur-xl"
                >
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

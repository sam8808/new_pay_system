<script setup>
import { ref, computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { 
    CheckCircle, 
    XCircle, 
    AlertCircle,
    Search,
    Download,
    Filter,
    ArrowUpDown,
    Eye
} from 'lucide-vue-next'

// Пропсы
const props = defineProps({
    transactions: {
        type: Object,
        required: true
    }
})

// Состояния
const searchQuery = ref('')
const selectedStatus = ref('all')
const selectedType = ref('all')
const sortDirection = ref('desc')
const showDetails = ref(null)
const selectedRows = ref(new Set())

// Фильтрация операций
const filteredTransaction = computed(() => {
    return props.transactions.data.filter(transaction => {
        const matchesSearch = transaction.id.toString().includes(searchQuery.value) ||
                            searchQuery.value === '';
        
        const matchesStatus = selectedStatus.value === 'all' ? true :
            selectedStatus.value === 'confirmed' ? transaction.confirmed :
            selectedStatus.value === 'canceled' ? transaction.canceled : true;
            
        const matchesType = selectedType.value === 'all' ? true :
        transaction.type === selectedType.value;
            
        return matchesSearch && matchesStatus && matchesType;
    });
})

// Форматирование даты
const formatDate = (dateString) => {
    const date = new Date(dateString)
    return new Intl.DateTimeFormat('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date)
}

// Форматирование суммы
const formatAmount = (amount) => {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        minimumFractionDigits: 2
    }).format(amount)
}

// Получение статуса операции
const getTransactionStatus = (transaction) => {
    if (transaction.confirmed) {
        return {
            icon: CheckCircle,
            class: 'bg-green-100 text-green-700',
            text: 'Подтверждено'
        }
    }
    if (transaction.canceled) {
        return {
            icon: XCircle,
            class: 'bg-red-100 text-red-700',
            text: 'Отменено'
        }
    }
    return {
        icon: AlertCircle,
        class: 'bg-yellow-100 text-yellow-700',
        text: 'В обработке'
    }
}

// Переключение выбора строки
const toggleRowSelection = (id) => {
    if (selectedRows.value.has(id)) {
        selectedRows.value.delete(id)
    } else {
        selectedRows.value.add(id)
    }
}
</script>

<template>
    <DashboardLayout>
        <div class="container mx-auto px-4 py-8">
            <!-- Заголовок и управляющие элементы -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <h1 class="text-2xl font-semibold text-slate-800">История операций</h1>
                
                <div class="flex items-center gap-3">
                    <button class="btn-primary">
                        <Filter class="w-4 h-4 mr-2" />
                        Фильтры
                    </button>
                </div>
            </div>

            <!-- Панель фильтров -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Поиск -->
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Поиск по ID..."
                            class="form-input pl-10"
                        />
                    </div>

                    <!-- Фильтр по статусу -->
                    <select v-model="selectedStatus" class="form-input">
                        <option value="all">Все статусы</option>
                        <option value="confirmed">Подтверждено</option>
                        <option value="canceled">Отменено</option>
                        <option value="pending">В обработке</option>
                    </select>

                    <!-- Фильтр по типу -->
                    <select v-model="selectedType" class="form-input">
                        <option value="all">Все типы</option>
                        <option value="payIn">Пополнения</option>
                        <option value="payOut">Выводы</option>
                    </select>

                    <!-- Сортировка -->
                    <button 
                        @click="sortDirection = sortDirection === 'asc' ? 'desc' : 'asc'"
                        class="flex items-center justify-center gap-2 btn-secondary"
                    >
                        <ArrowUpDown class="w-4 h-4" />
                        {{ sortDirection === 'asc' ? 'Сначала старые' : 'Сначала новые' }}
                    </button>
                </div>
            </div>

            <!-- Таблица -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Дата
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Тип
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Сумма
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Валюта
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Статус
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">
                                    Действия
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr 
                                v-for="transaction in filteredTransaction" 
                                :key="transaction.id"
                                class="hover:bg-slate-50 transition-colors"
                                :class="{ 'bg-blue-50/50': selectedRows.has(transaction.id) }"
                            >
                                <!-- ID -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-slate-900">
                                        #{{ transaction.id }}
                                    </span>
                                </td>

                                <!-- Дата -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900">
                                        {{ formatDate(transaction.created_at) }}
                                    </div>
                                </td>

                                <!-- Тип -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            transaction.type === 'payIn' 
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800'
                                        ]"
                                    >
                                        {{ transaction.type === 'payIn' ? 'Пополнение' : 'Вывод' }}
                                    </span>
                                </td>

                                <!-- Сумма -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div 
                                        :class="[
                                            'text-sm font-medium',
                                            transaction.type === 'payIn' ? 'text-green-600' : 'text-red-600'
                                        ]"
                                    >
                                        {{ formatAmount(transaction.amount) }}
                                    </div>
                                </td>

                                <!-- Валюта -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-slate-900">
                                        {{ transaction.currency }}
                                    </div>
                                </td>

                                <!-- Статус -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div 
                                        :class="[
                                            'inline-flex items-center px-2.5 py-1.5 rounded-full text-xs font-medium',
                                            getTransactionStatus(transaction).class
                                        ]"
                                    >
                                        <component 
                                            :is="getTransactionStatus(transaction).icon"
                                            class="w-4 h-4 mr-1.5"
                                        />
                                        {{ getTransactionStatus(transaction).text }}
                                    </div>
                                </td>

                                <!-- Действия -->
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <button 
                                        @click="showDetails = showDetails === transaction.id ? null : transaction.id"
                                        class="btn-icon"
                                    >
                                        <Eye class="w-4 h-4" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="border-t border-slate-200">
                    <Pagination :links="transactions.links" class="px-4" />
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.btn-primary {
    @apply inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg
    hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
    transition-colors duration-200;
}

.btn-secondary {
    @apply inline-flex items-center px-4 py-2 bg-white text-slate-700 text-sm font-medium rounded-lg
    border border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 
    focus:ring-slate-500 transition-colors duration-200;
}

.btn-icon {
    @apply p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg
    transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500;
}

/* .form-input {
    @apply block w-full px-3 py-2 text-sm border border-slate-300 rounded-lg
    focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
    transition-colors duration-200;
} */
</style>

<script setup>
import { ref, computed } from "vue";
import {
    Users,
    Link as LinkIcon,
    Copy,
    ArrowUpRight,
    UserPlus,
    Percent,
    Clock,
    ChevronDown,
} from "lucide-vue-next";

// Пропсы
defineProps({
    referralStats: {
        type: Object,
        default: () => ({
            totalEarnings: 15000,
            activeReferrals: 12,
            totalReferrals: 25,
            conversionRate: 48,
            referralLink: "https://example.com/ref/user123",
            referralCode: "USER123",
        }),
    },
    referrals: {
        type: Array,
        default: () => [
            {
                id: 1,
                email: "partner1@example.com",
                registrationDate: "2024-02-15",
                totalEarned: 5000,
                status: "active",
                lastActivity: "2024-02-20",
            },
            // ... другие рефералы
        ],
    },
});

// Состояния
const timeFilter = ref("all"); // all, week, month, year
const sortBy = ref("date"); // date, earnings
const sortOrder = ref("desc");
const showCopiedMessage = ref(false);

// Копирование реферальной ссылки
const copyReferralLink = () => {
    navigator.clipboard.writeText(props.referralStats.referralLink);
    showCopiedMessage.value = true;
    setTimeout(() => {
        showCopiedMessage.value = false;
    }, 2000);
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("ru-RU", {
        style: "currency",
        currency: "RUB",
        minimumFractionDigits: 2,
    }).format(amount);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};
</script>

<template>
    <DashboardLayout>
        <div class="container mx-auto px-6 py-8">
            <!-- Заголовок -->
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4"
            >
                <div>
                    <h1 class="text-2xl font-medium text-gray-900">
                        Партнерская программа
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Приглашайте партнеров и получайте вознаграждение
                    </p>
                </div>
            </div>

            <!-- Реферальная ссылка -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Ссылка -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Ваша реферальная ссылка
                            </label>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 relative">
                                    <LinkIcon
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    />
                                    <input
                                        type="text"
                                        :value="referralStats.referralLink"
                                        readonly
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 font-mono"
                                    />
                                </div>
                                <button
                                    @click="copyReferralLink"
                                    class="inline-flex items-center px-4 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 transition-colors"
                                >
                                    <Copy class="w-4 h-4 mr-2" />
                                    Копировать
                                </button>
                            </div>
                            <!-- Сообщение о копировании -->
                            <div
                                v-show="showCopiedMessage"
                                class="absolute mt-2 px-3 py-1 bg-gray-900 text-white text-sm rounded-lg"
                            >
                                Ссылка скопирована
                            </div>
                        </div>

                        <!-- Код -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Ваш реферальный код
                            </label>
                            <div class="flex items-center gap-2">
                                <div class="flex-1 relative">
                                    <UserPlus
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    />
                                    <input
                                        type="text"
                                        :value="referralStats.referralCode"
                                        readonly
                                        class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 font-mono"
                                    />
                                </div>
                                <button
                                    @click="copyReferralLink"
                                    class="inline-flex items-center px-4 py-2.5 border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors"
                                >
                                    <Copy class="w-4 h-4 mr-2" />
                                    Копировать
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Статистика -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"
            >
                <!-- Общий заработок -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-12 h-12 bg-violet-50 rounded-xl flex items-center justify-center"
                        >
                            <ArrowUpRight class="w-6 h-6 text-violet-600" />
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500">
                        Общий заработок
                    </p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        {{ formatCurrency(referralStats.totalEarnings) }}
                    </p>
                </div>

                <!-- Активные рефералы -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center"
                        >
                            <Users class="w-6 h-6 text-emerald-600" />
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500">
                        Активные рефералы
                    </p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        {{ referralStats.activeReferrals }}
                    </p>
                </div>

                <!-- Всего рефералов -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center"
                        >
                            <UserPlus class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500">
                        Всего рефералов
                    </p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        {{ referralStats.totalReferrals }}
                    </p>
                </div>

                <!-- Конверсия -->
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-3">
                        <div
                            class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center"
                        >
                            <Percent class="w-6 h-6 text-amber-600" />
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Конверсия</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">
                        {{ referralStats.conversionRate }}%
                    </p>
                </div>
            </div>

            <!-- Список рефералов -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h2 class="font-medium text-gray-900">Ваши рефералы</h2>

                        <!-- Фильтры -->
                        <div class="flex items-center gap-3">
                            <select
                                v-model="timeFilter"
                                class="pl-3 pr-8 py-1.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                            >
                                <option value="all">За все время</option>
                                <option value="week">За неделю</option>
                                <option value="month">За месяц</option>
                                <option value="year">За год</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Таблица рефералов -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Реферал
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Дата регистрации
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Заработок
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Статус
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Последняя активность
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            <tr
                                v-for="referral in referrals"
                                :key="referral.id"
                                class="hover:bg-gray-50/50"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full bg-violet-100 flex items-center justify-center"
                                        >
                                            <span
                                                class="text-sm font-medium text-violet-700"
                                            >
                                                {{
                                                    referral.email
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </span>
                                        </div>
                                        <div>
                                            <p
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ referral.email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                >
                                    {{ formatDate(referral.registrationDate) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="text-sm font-medium text-gray-900"
                                    >
                                        {{
                                            formatCurrency(referral.totalEarned)
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700':
                                                referral.status === 'active',
                                            'bg-gray-50 text-gray-700':
                                                referral.status !== 'active',
                                        }"
                                    >
                                        {{
                                            referral.status === "active"
                                                ? "Активный"
                                                : "Неактивный"
                                        }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div
                                        class="flex items-center text-sm text-gray-500"
                                    >
                                        <Clock
                                            class="w-4 h-4 mr-1.5 text-gray-400"
                                        />
                                        {{ formatDate(referral.lastActivity) }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация или "Загрузить еще" -->
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    <button
                        class="text-sm text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1"
                    >
                        Показать больше
                        <ChevronDown class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

<style scoped>
/* Стилизация селекта */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
</style>

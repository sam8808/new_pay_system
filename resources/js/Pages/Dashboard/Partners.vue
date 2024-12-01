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
    <AccountLayout>
        <div class="container mx-auto px-8 py-8">
            <!-- Заголовок -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
                <div class="space-y-2">
                    <h1 class="text-2xl font-semibold text-gray-900">Партнерская программа</h1>
                    <p class="text-sm text-gray-500">Приглашайте партнеров и получайте вознаграждение</p>
                </div>
            </div>

            <!-- Реферальная ссылка -->
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden mb-8 backdrop-blur-xl">
                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Ссылка -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">
                                Ваша реферальная ссылка
                            </label>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 relative">
                                    <LinkIcon class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input type="text"
                                           :value="referralStats.referralLink"
                                           readonly
                                           class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 font-mono focus:ring-2 focus:ring-violet-500 transition-all duration-300" />
                                </div>
                                <button @click="copyReferralLink"
                                        class="inline-flex items-center px-5 py-3 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 shadow-lg shadow-violet-600/20 transition-all duration-300">
                                    <Copy class="w-4 h-4 mr-2" />
                                    Копировать
                                </button>
                            </div>
                        </div>

                        <!-- Код -->
                        <div class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">
                                Ваш реферальный код
                            </label>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 relative">
                                    <UserPlus class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                                    <input type="text"
                                           :value="referralStats.referralCode"
                                           readonly
                                           class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-900 font-mono focus:ring-2 focus:ring-violet-500 transition-all duration-300" />
                                </div>
                                <button @click="copyReferralLink"
                                        class="inline-flex items-center px-5 py-3 border border-gray-200 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-300">
                                    <Copy class="w-4 h-4 mr-2" />
                                    Копировать
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Статистика -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Общий заработок -->
                <div class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-violet-50 to-violet-100 rounded-xl flex items-center justify-center">
                            <ArrowUpRight class="w-6 h-6 text-violet-600" />
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Общий заработок</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-2">
                        {{ formatCurrency(referralStats.totalEarnings) }}
                    </p>
                </div>

                <!-- Активные рефералы -->
                <div class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl flex items-center justify-center">
                            <Users class="w-6 h-6 text-emerald-600" />
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Активные рефералы</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-2">
                        {{ referralStats.activeReferrals }}
                    </p>
                </div>

                <!-- Всего рефералов -->
                <div class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                            <UserPlus class="w-6 h-6 text-blue-600" />
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Всего рефералов</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-2">
                        {{ referralStats.totalReferrals }}
                    </p>
                </div>

                <!-- Конверсия -->
                <div class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 p-6 hover:shadow-xl transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl flex items-center justify-center">
                            <Percent class="w-6 h-6 text-amber-600" />
                        </div>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Конверсия</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-2">
                        {{ referralStats.conversionRate }}%
                    </p>
                </div>
            </div>

            <!-- Список рефералов -->
            <div class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden backdrop-blur-xl">
                <div class="px-8 py-5 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Ваши рефералы</h2>

                        <!-- Фильтры -->
                        <div class="flex items-center gap-4">
                            <select v-model="timeFilter"
                                    class="pl-4 pr-10 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300">
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
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50/80">
                                <th class="px-8 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Реферал
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Дата регистрации
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Заработок
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Статус
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Последняя активность
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="referral in referrals"
                                :key="referral.id"
                                class="hover:bg-gray-50/50 transition-all duration-300">
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-100 to-violet-200 flex items-center justify-center">
                                            <span class="text-sm font-semibold text-violet-700">
                                                {{ referral.email.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ referral.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(referral.registrationDate) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">
                                        {{ formatCurrency(referral.totalEarned) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium"
                                          :class="{
                                              'bg-emerald-50 text-emerald-700': referral.status === 'active',
                                              'bg-gray-50 text-gray-700': referral.status !== 'active'
                                          }">
                                        {{ referral.status === 'active' ? 'Активный' : 'Неактивный' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <Clock class="w-4 h-4 mr-2 text-gray-400" />
                                        {{ formatDate(referral.lastActivity) }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="px-8 py-4 border-t border-gray-100 bg-gray-50/80">
                    <button class="text-sm text-violet-600 hover:text-violet-700 font-medium flex items-center gap-1.5 transition-colors">
                        Показать больше
                        <ChevronDown class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </AccountLayout>
</template>

<style scoped>
/* Стилизация селекта */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 1rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.75rem;
    -webkit-appearance: none;
    appearance: none;
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
<script setup>
import VueApexCharts from "vue3-apexcharts";
import { Head } from "@inertiajs/vue3";
import {
    UserRound,
    Store,
    CreditCard,
    Wallet,
    TrendingUp,
    TrendingDown,
} from "lucide-vue-next";
import AdminLayout from "@/Layouts/AdminLayout.vue";

const props = defineProps({
    statistics: {
        type: Object,
        required: true,
    },
});

const formatMoney = (amount) => {
    return new Intl.NumberFormat("ru-RU", {
        style: "decimal",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount);
};

const chartOptions = {
    chart: {
        type: "area",
        toolbar: { show: false },
        sparkline: { enabled: true },
        background: 'transparent',
    },
    stroke: {
        curve: "smooth",
        width: 2,
        colors: ['#10B981']
    },
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.45,
            opacityTo: 0.05,
            stops: [0, 100],
            colorStops: [
                {
                    offset: 0,
                    color: '#10B981',
                    opacity: 0.45
                },
                {
                    offset: 100,
                    color: '#10B981',
                    opacity: 0.05
                }
            ]
        },
    },
    tooltip: { enabled: false },
    theme: {
        mode: 'light'
    }
};

const sampleChartData = {
    series: [
        {
            name: "Trend",
            data: [31, 40, 28, 51, 42, 109, 100],
        },
    ],
};
</script>

<template>
    <Head title="Панель администратора" />
    <AdminLayout>
        <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 relative overflow-hidden">
            <!-- Animated Background Elements -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute top-1/4 -left-10 w-72 h-72 bg-emerald-500/20 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-1/4 -right-10 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: -7s;"></div>
                <div class="absolute top-1/3 right-1/4 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: -14s;"></div>
            </div>

            <div class="container mx-auto px-4 py-8 relative">
                <div class="space-y-8">
                    <!-- Main Stats Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Users Card -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-emerald-500/10 rounded-xl">
                                    <UserRound class="w-6 h-6 text-emerald-500" />
                                </div>
                                <div class="flex items-center text-emerald-500 text-sm font-medium bg-emerald-500/10 px-2 py-1 rounded-lg">
                                    <span>+{{ statistics.usersCountToday }}</span>
                                    <TrendingUp class="w-4 h-4 ml-1" />
                                </div>
                            </div>
                            <h3 class="text-gray-600 text-sm font-medium mb-2">Пользователи</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-gray-800">{{ statistics.usersCount }}</span>
                                <span class="text-gray-600 font-medium">Чел.</span>
                            </div>
                            <div class="mt-4 h-16">
                                <VueApexCharts
                                    type="area"
                                    height="100%"
                                    :options="chartOptions"
                                    :series="sampleChartData.series"
                                />
                            </div>
                        </div>

                        <!-- Merchants Card -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-purple-500/10 rounded-xl">
                                    <Store class="w-6 h-6 text-purple-500" />
                                </div>
                                <div class="flex items-center text-emerald-500 text-sm font-medium bg-emerald-500/10 px-2 py-1 rounded-lg">
                                    <span>+{{ statistics.merchantsCountToday }}</span>
                                    <TrendingUp class="w-4 h-4 ml-1" />
                                </div>
                            </div>
                            <h3 class="text-gray-600 text-sm font-medium mb-2">Магазины</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-gray-800">{{ statistics.merchantsCount }}</span>
                                <span class="text-gray-600 font-medium">Шт.</span>
                            </div>
                            <div class="mt-4 h-16">
                                <VueApexCharts
                                    type="area"
                                    height="100%"
                                    :options="chartOptions"
                                    :series="sampleChartData.series"
                                />
                            </div>
                        </div>

                        <!-- Payments Card -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-emerald-500/10 rounded-xl">
                                    <CreditCard class="w-6 h-6 text-emerald-500" />
                                </div>
                                <div class="flex items-center text-emerald-500 text-sm font-medium bg-emerald-500/10 px-2 py-1 rounded-lg">
                                    <span>+{{ formatMoney(statistics.approvedPaymentsSumToday) }}</span>
                                    <TrendingUp class="w-4 h-4 ml-1" />
                                </div>
                            </div>
                            <h3 class="text-gray-600 text-sm font-medium mb-2">Платежи</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-gray-800">{{ formatMoney(statistics.approvedPaymentsSum) }}</span>
                                <span class="text-gray-600 font-medium">₽</span>
                            </div>
                            <div class="mt-4 h-16">
                                <VueApexCharts
                                    type="area"
                                    height="100%"
                                    :options="chartOptions"
                                    :series="sampleChartData.series"
                                />
                            </div>
                        </div>

                        <!-- Withdrawals Card -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20">
                            <div class="flex items-center justify-between mb-4">
                                <div class="p-3 bg-rose-500/10 rounded-xl">
                                    <Wallet class="w-6 h-6 text-rose-500" />
                                </div>
                                <div class="flex items-center text-emerald-500 text-sm font-medium bg-emerald-500/10 px-2 py-1 rounded-lg">
                                    <span>+{{ formatMoney(statistics.approvedWithdrawalsSumToday) }}</span>
                                    <TrendingUp class="w-4 h-4 ml-1" />
                                </div>
                            </div>
                            <h3 class="text-gray-600 text-sm font-medium mb-2">Выплаты</h3>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-gray-800">{{ formatMoney(statistics.approvedWithdrawalsSum) }}</span>
                                <span class="text-gray-600 font-medium">₽</span>
                            </div>
                            <div class="mt-4 h-16">
                                <VueApexCharts
                                    type="area"
                                    height="100%"
                                    :options="chartOptions"
                                    :series="sampleChartData.series"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Weekly & Monthly Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Weekly Payments -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20 group">
                            <div class="mb-4">
                                <div class="p-3 bg-emerald-500/10 rounded-xl w-fit group-hover:bg-emerald-500/20 transition-colors">
                                    <TrendingDown class="w-6 h-6 text-emerald-500" />
                                </div>
                            </div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl font-bold text-gray-800">{{ formatMoney(statistics.approvedPaymentsSumLast7Days) }}</span>
                                <span class="text-gray-600 font-medium">₽</span>
                            </div>
                            <p class="text-gray-600 text-sm">Сумма платежей за последние 7 дней</p>
                        </div>

                        <!-- Weekly Withdrawals -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20 group">
                            <div class="mb-4">
                                <div class="p-3 bg-rose-500/10 rounded-xl w-fit group-hover:bg-rose-500/20 transition-colors">
                                    <TrendingUp class="w-6 h-6 text-rose-500" />
                                </div>
                            </div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl font-bold text-gray-800">{{ formatMoney(statistics.approvedWithdrawalsSumLast7Days) }}</span>
                                <span class="text-gray-600 font-medium">₽</span>
                            </div>
                            <p class="text-gray-600 text-sm">Сумма выплат за последние 7 дней</p>
                        </div>

                        <!-- Monthly Payments -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20 group">
                            <div class="mb-4">
                                <div class="p-3 bg-emerald-500/10 rounded-xl w-fit group-hover:bg-emerald-500/20 transition-colors">
                                    <TrendingDown class="w-6 h-6 text-emerald-500" />
                                </div>
                            </div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl font-bold text-gray-800">{{ formatMoney(statistics.approvedPaymentsSumThisMonth) }}</span>
                                <span class="text-gray-600 font-medium">₽</span>
                            </div>
                            <p class="text-gray-600 text-sm">Сумма платежей за текущий месяц</p>
                        </div>

                        <!-- Monthly Withdrawals -->
                        <div class="bg-white/10 backdrop-blur-xl rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-white/20 group">
                            <div class="mb-4">
                                <div class="p-3 bg-rose-500/10 rounded-xl w-fit group-hover:bg-rose-500/20 transition-colors">
                                    <TrendingUp class="w-6 h-6 text-rose-500" />
                                </div>
                            </div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-2xl font-bold text-gray-800">{{ formatMoney(statistics.approvedWithdrawalsSumThisMonth) }}</span>
                                <span class="text-gray-600 font-medium">₽</span>
                            </div>
                            <p class="text-gray-600 text-sm">Сумма выплат за текущий месяц</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
@keyframes pulse {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 0.2; }
}

@keyframes float {
    0%, 100% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-20px) scale(1.05); }
}

.animate-pulse {
    animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

:deep(.apexcharts-canvas) {
    width: 100% !important;
    background: transparent !important;
}

:deep(.apexcharts-element-hidden),
:deep(.apexcharts-gridlines-hidden) {
    opacity: 0.3;
}
</style>
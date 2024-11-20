<script setup>
import { ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import {
    ArrowLeft,
    Wallet,
    CreditCard,
    Building,
    AlertCircle,
    CheckCircle,
} from "lucide-vue-next";

defineProps({
    merchants: {
        type: Array,
        required: true,
    },
    paymentSystems: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    amount: "",
    m_id: "",
    details: "",
    ps_id: "",
});

const submit = () => {
    form.post(route("withdrawal.store"));
};
</script>

<template>
    <DashboardLayout>
        <div class="container mx-auto px-6 py-8">
            <!-- Шапка -->
            <div class="flex flex-col gap-6 mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Link
                            :href="route('dashboard')"
                            class="flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors"
                        >
                            <ArrowLeft class="w-5 h-5" />
                        </Link>
                        <div>
                            <h1 class="text-2xl font-medium text-gray-900">
                                Вывод средств
                            </h1>
                            <p class="text-sm text-gray-500 mt-1">
                                Заполните форму для создания заявки на вывод
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Форма -->
            <form @submit.prevent="submit" class="max-w-5xl">
                <!-- Основная информация -->
                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6"
                >
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h2 class="font-medium text-gray-900">
                            Информация о выводе
                        </h2>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Сумма и выбор баланса -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Сумма -->
                            <div>
                                <label
                                    for="amount"
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Сумма вывода
                                </label>
                                <div class="relative">
                                    <Wallet
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    />
                                    <input
                                        id="amount"
                                        v-model="form.amount"
                                        type="text"
                                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                                        placeholder="Введите сумму"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.amount"
                                    class="mt-1.5 text-sm text-red-600"
                                >
                                    {{ form.errors.amount }}
                                </p>
                            </div>

                            <!-- Выбор баланса -->
                            <div>
                                <label
                                    for="m_id"
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Баланс магазина
                                </label>
                                <div class="relative">
                                    <Building
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    />
                                    <select
                                        id="m_id"
                                        v-model="form.m_id"
                                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors appearance-none"
                                    >
                                        <option value="">
                                            Выберите баланс
                                        </option>
                                        <option
                                            v-for="merchant in merchants"
                                            :key="merchant.id"
                                            :value="merchant.id"
                                        >
                                            {{ merchant.balance }} ({{
                                                merchant.title
                                            }})
                                        </option>
                                    </select>
                                </div>
                                <p
                                    v-if="form.errors.m_id"
                                    class="mt-1.5 text-sm text-red-600"
                                >
                                    {{ form.errors.m_id }}
                                </p>
                            </div>
                        </div>

                        <!-- Реквизиты и платежная система -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Реквизиты -->
                            <div>
                                <label
                                    for="details"
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Реквизиты
                                </label>
                                <div class="relative">
                                    <CreditCard
                                        class="absolute left-3 top-3 w-5 h-5 text-gray-400"
                                    />
                                    <textarea
                                        id="details"
                                        v-model="form.details"
                                        rows="3"
                                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors resize-none"
                                        placeholder="Введите реквизиты для вывода"
                                    ></textarea>
                                </div>
                                <p
                                    v-if="form.errors.details"
                                    class="mt-1.5 text-sm text-red-600"
                                >
                                    {{ form.errors.details }}
                                </p>
                            </div>

                            <!-- Платежная система -->
                            <div>
                                <label
                                    for="ps_id"
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Платежная система
                                </label>
                                <div class="relative">
                                    <Building
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    />
                                    <select
                                        id="ps_id"
                                        v-model="form.ps_id"
                                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors appearance-none"
                                    >
                                        <option value="">
                                            Выберите платежную систему
                                        </option>
                                        <option
                                            v-for="ps in paymentSystems"
                                            :key="ps.id"
                                            :value="ps.id"
                                        >
                                            {{ ps.title }} ({{ ps.currency }})
                                        </option>
                                    </select>
                                </div>
                                <p
                                    v-if="form.errors.ps_id"
                                    class="mt-1.5 text-sm text-red-600"
                                >
                                    {{ form.errors.ps_id }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Предупреждение -->
                <div
                    class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6"
                >
                    <div class="flex items-start gap-3">
                        <AlertCircle class="w-5 h-5 text-amber-500 mt-0.5" />
                        <div>
                            <h3 class="text-sm font-medium text-amber-800">
                                Важная информация
                            </h3>
                            <p class="text-sm text-amber-700 mt-1">
                                Перед подтверждением вывода, пожалуйста,
                                проверьте правильность введенных реквизитов.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Сообщения об ошибках/успехе -->
                <div v-if="$page.props.flash.message" class="mb-6">
                    <div
                        class="flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700"
                    >
                        <AlertCircle class="w-5 h-5" />
                        <p class="text-sm">{{ $page.props.flash.message }}</p>
                    </div>
                </div>

                <div v-if="$page.props.flash.success" class="mb-6">
                    <div
                        class="flex items-center gap-3 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700"
                    >
                        <CheckCircle class="w-5 h-5" />
                        <p class="text-sm">{{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="flex items-center justify-end gap-3">
                    <Link
                        :href="route('merchant')"
                        class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors"
                    >
                        Отмена
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Создать заявку
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>

<style scoped>
/* Стилизация селекта */
select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}
</style>

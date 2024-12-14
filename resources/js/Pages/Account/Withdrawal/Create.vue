<script setup>
import { useForm } from "@inertiajs/vue3";
import {
    ArrowLeft,
    Wallet,
    CreditCard,
    Building,
    AlertCircle,
    CheckCircle,
} from "lucide-vue-next";

defineProps({
    wallets: {
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
    wallet: "",
    details: "",
    paymentSystem: "",
});

const submit = () => {
    form.post(route("withdrawal.store"));
};
</script>

<template>
    <AccountLayout>
        <div class="container mx-auto px-8 py-8">
            <!-- Шапка -->
            <div class="flex flex-col gap-6 mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <Link
                            :href="route('dashboard')"
                            class="flex items-center justify-center w-11 h-11 rounded-xl border border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-all duration-300 hover:border-gray-300"
                        >
                            <ArrowLeft class="w-5 h-5" />
                        </Link>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">
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
            <form @submit.prevent="submit" class="max-w-5xl space-y-6">
                <!-- Основная информация -->
                <div
                    class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden backdrop-blur-xl"
                >
                    <div class="px-8 py-5 border-b border-gray-100">
                        <h2 class="font-medium text-gray-900">
                            Информация о выводе
                        </h2>
                    </div>

                    <div class="p-8 space-y-8">
                        <!-- Сумма и выбор баланса -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Сумма -->
                            <div class="space-y-2">
                                <label
                                    for="amount"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Сумма вывода
                                </label>
                                <div class="relative">
                                    <Wallet
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    />
                                    <input
                                        id="amount"
                                        v-model="form.amount"
                                        type="text"
                                        class="w-full pl-12 pr-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300"
                                        placeholder="Введите сумму"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.amount"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.amount }}
                                </p>
                            </div>

                            <!-- Выбор баланса -->
                            <div class="space-y-2">
                                <div v-if="wallets.length">
                                    <label
                                        for="wallet"
                                        class="block text-sm font-medium text-gray-700"
                                    >
                                        Баланс
                                    </label>
                                    <div class="relative">
                                        <Building
                                            class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                        />
                                        <select
                                            id="wallet"
                                            v-model="form.wallet"
                                            class="w-full pl-12 pr-10 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 appearance-none"
                                        >
                                            <option value="">
                                                Выберите баланс
                                            </option>
                                            <option
                                                v-for="wallet in wallets"
                                                :key="wallet.id"
                                                :value="wallet.id"
                                            >
                                                {{ wallet.balance }} ({{
                                                    wallet.currency.code
                                                }})
                                            </option>
                                        </select>
                                    </div>
                                    <p
                                        v-if="form.errors.wallet"
                                        class="text-sm text-red-600"
                                    >
                                        {{ form.errors.wallet }}
                                    </p>
                                </div>
                                <div v-else>
                                    <p>Нет доступных кошельков для вывода.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Реквизиты и платежная система -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Реквизиты -->
                            <div class="space-y-2">
                                <label
                                    for="details"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Реквизиты
                                </label>
                                <div class="relative">
                                    <CreditCard
                                        class="absolute left-4 top-3 w-5 h-5 text-gray-400"
                                    />
                                    <textarea
                                        id="details"
                                        v-model="form.details"
                                        rows="3"
                                        class="w-full pl-12 pr-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 resize-none"
                                        placeholder="Введите реквизиты для вывода"
                                    >
                                    </textarea>
                                </div>
                                <p
                                    v-if="form.errors.details"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.details }}
                                </p>
                            </div>

                            <!-- Платежная система -->
                            <div class="space-y-2">
                                <label
                                    for="ps_id"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Платежная система
                                </label>
                                <div class="relative">
                                    <Building
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    />
                                    <select
                                        id="ps_id"
                                        v-model="form.paymentSystem"
                                        class="w-full pl-12 pr-10 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 appearance-none"
                                    >
                                        <option value="">
                                            Выберите платежную систему
                                        </option>
                                        <option
                                            v-for="paymentSystem in paymentSystems"
                                            :key="paymentSystem.id"
                                            :value="paymentSystem.id"
                                        >
                                            {{ paymentSystem.title }} ({{ paymentSystem.currency.code }})
                                        </option>
                                    </select>
                                </div>
                                <p
                                    v-if="form.errors.ps_id"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.ps_id }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Предупреждение -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
                    <div class="flex items-start gap-4">
                        <AlertCircle
                            class="w-5 h-5 text-amber-500 mt-0.5 flex-shrink-0"
                        />
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

                <!-- Сообщения -->
                <div
                    v-if="$page.props.flash.message"
                    class="flex items-center gap-4 p-5 bg-red-50 border border-red-200 rounded-xl text-red-700"
                >
                    <AlertCircle class="w-5 h-5 flex-shrink-0" />
                    <p class="text-sm">{{ $page.props.flash.message }}</p>
                </div>

                <div
                    v-if="$page.props.flash.success"
                    class="flex items-center gap-4 p-5 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700"
                >
                    <CheckCircle class="w-5 h-5 flex-shrink-0" />
                    <p class="text-sm">{{ $page.props.flash.success }}</p>
                </div>

                <!-- Кнопки действий -->
                <div class="flex items-center justify-end gap-4">
                    <Link
                        :href="route('merchant')"
                        class="inline-flex items-center px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all duration-300"
                    >
                        Отмена
                    </Link>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center px-5 py-3 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 shadow-lg shadow-violet-600/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
                    >
                        Создать заявку
                    </button>
                </div>
            </form>
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
}

/* Улучшенные эффекты */
.shadow-lg {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Эффект размытия */
.backdrop-blur-xl {
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
}

/* Стилизация полей ввода при фокусе */
input:focus,
select:focus,
textarea:focus {
    box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
}
</style>

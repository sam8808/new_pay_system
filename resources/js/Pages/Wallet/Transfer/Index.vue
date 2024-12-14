<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import AccountLayout from "@/Layouts/AccountLayout.vue";
import debounce from "lodash/debounce";

// Определяем типы входных параметров через defineProps
const props = defineProps({
    wallets: {
        type: Array,
        required: true,
        // Здесь мы определяем структуру кошелька для лучшей типизации
        default: () => [],
    },
    recentTransfers: {
        type: Array,
        required: true,
        default: () => [],
    },
    hasActiveWallets: {
        type: Boolean,
        required: true,
    },
});

// Состояния для управления UI
const processing = ref(false);
const recipientInfo = ref(null);

// Создаем форму с использованием Inertia useForm
const form = useForm({
    from_wallet_id: "",
    to_account: "",
    amount: "",
    description: "",
});

// Вычисляемое свойство для отображения валюты выбранного кошелька
const selectedWalletCurrency = computed(() => {
    // Находим выбранный кошелек и возвращаем его валюту
    const selectedWallet = props.wallets.find(
        (wallet) => wallet.id === form.from_wallet_id
    );
    return selectedWallet?.currency || "";
});

// Функция проверки получателя с debounce для оптимизации запросов
const checkRecipient = debounce(async () => {
    // Очищаем информацию о получателе если длина меньше 3 символов
    if (form.to_account.length < 3) {
        recipientInfo.value = null;
        return;
    }

    try {
        // Отправляем запрос на проверку получателя
        const response = await axios.post(route("wallet.transfers.check"), {
            to_account: form.to_account,
            from_wallet_id: form.from_wallet_id,
        });

        // Обновляем информацию о получателе в зависимости от ответа
        recipientInfo.value = response.data.success
            ? response.data.recipient
            : null;
    } catch (error) {
        recipientInfo.value = null;
        console.error("Error checking recipient:", error);
    }
}, 300);

// Функция отправки формы
const submitForm = async () => {
    processing.value = true;

    try {
        // Отправляем форму используя Inertia
        await form.post(route("wallet.transfers.store"), {
            preserveScroll: true,
            onSuccess: () => {
                // Сбрасываем форму и очищаем информацию о получателе при успехе
                form.reset();
                recipientInfo.value = null;
            },
        });
    } catch (error) {
        console.error("Error submitting form:", error);
    } finally {
        processing.value = false;
    }
};

// Функция форматирования даты для отображения в списке
const formatTransferDate = (date) => {
    return new Date(date).toLocaleString();
};

// Вычисляемое свойство для проверки возможности отправки формы
const canSubmit = computed(() => {
    return (
        !processing.value &&
        form.from_wallet_id &&
        form.to_account &&
        form.amount > 0 &&
        recipientInfo.value
    );
});

// Функция для форматирования суммы с учетом валюты
const formatAmount = (amount, currency) => {
    return `${Number(amount).toFixed(8)} ${currency}`;
};
</script>

<template>
    <!-- Оставляем тот же шаблон, но добавляем новые вычисляемые свойства и форматирование -->
    <AccountLayout title="Transfer Funds">
        <!-- Заголовок -->
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ "Transfer Funds" }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Форма перевода -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Сообщение об отсутствии активных кошельков -->
                        <div v-if="!hasActiveWallets" class="text-center">
                            <p class="text-gray-600">
                                {{
                                    "You don't have any active wallets. Please create one first."
                                }}
                            </p>
                            <Link
                                :href="route('wallet.create')"
                                class="mt-4 inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                            >
                                {{ "Create Wallet" }}
                            </Link>
                        </div>

                        <!-- Форма перевода -->
                        <form
                            v-else
                            @submit.prevent="submitForm"
                            class="space-y-6"
                        >
                            <!-- Выбор кошелька -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    {{ "From Wallet" }}
                                </label>
                                <select
                                    v-model="form.from_wallet_id"
                                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                                >
                                    <option value="">
                                        {{ "Select wallet" }}
                                    </option>
                                    <option
                                        v-for="wallet in wallets"
                                        :key="wallet.id"
                                        :value="wallet.id"
                                    >
                                        {{
                                            formatAmount(
                                                wallet.balance,
                                                wallet.currency
                                            )
                                        }}
                                    </option>
                                </select>
                                <div
                                    v-if="form.errors.from_wallet_id"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.from_wallet_id }}
                                </div>
                            </div>

                            <!-- Остальные поля формы остаются теми же -->
                            <!-- ... -->

                            <!-- Кнопка отправки с улучшенной проверкой возможности отправки -->
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="!canSubmit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                                >
                                    <span v-if="processing" class="mr-2">
                                        <svg
                                            class="animate-spin h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                        >
                                            <circle
                                                class="opacity-25"
                                                cx="12"
                                                cy="12"
                                                r="10"
                                                stroke="currentColor"
                                                stroke-width="4"
                                            ></circle>
                                            <path
                                                class="opacity-75"
                                                fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                            ></path>
                                        </svg>
                                    </span>
                                    {{
                                        processing
                                            ? "Processing..."
                                            : "Continue"
                                    }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Список последних переводов с улучшенным форматированием -->
                <div
                    class="mt-8 overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ "Recent Transfers" }}
                        </h3>
                        <div class="mt-6">
                            <div
                                v-if="recentTransfers.length === 0"
                                class="text-center text-gray-500"
                            >
                                {{ "No recent transfers" }}
                            </div>
                            <div v-else class="space-y-4">
                                <div
                                    v-for="transfer in recentTransfers"
                                    :key="transfer.uuid"
                                    class="flex justify-between items-center p-4 border rounded-lg"
                                >
                                    <div>
                                        <p class="font-medium">
                                            {{ transfer.recipient }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{
                                                formatTransferDate(
                                                    transfer.date
                                                )
                                            }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium">
                                            {{
                                                formatAmount(
                                                    transfer.amount,
                                                    transfer.currency
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AccountLayout>
</template>

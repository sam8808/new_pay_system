<!-- resources/js/Pages/Account/Deposits/Index.vue -->
<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AccountLayout from "@/Layouts/AccountLayout.vue";

const props = defineProps({
    deposits: Object,
    paymentSystems: Array,
    currencies: Array,
});

const form = useForm({
    payment_system_id: "",
    currency_id: "",
    amount: "",
    payer_email: "",
    payer_phone: "",
});

const createDeposit = () => {
    form.post(route("deposit.store"));
};
</script>

<template>
    <AccountLayout>
        <div class="p-4 sm:p-8 bg-white shadow">
            <!-- Форма создания депозита -->
            <div class="max-w-xl">
                <h2 class="text-lg font-medium text-gray-900">
                    Create Deposit
                </h2>
                <form @submit.prevent="createDeposit" class="mt-6 space-y-6">
                    <div>
                        <label class="text-sm font-medium text-gray-700"
                            >Payment System</label
                        >
                        <select
                            v-model="form.payment_system_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                            <option
                                v-for="ps in paymentSystems"
                                :key="ps.id"
                                :value="ps.id"
                            >
                                {{ ps.title }}
                            </option>
                        </select>
                        <div
                            v-if="form.errors.payment_system_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.payment_system_id }}
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700"
                            >Currency</label
                        >
                        <select
                            v-model="form.currency_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        >
                            <option
                                v-for="currency in currencies"
                                :key="currency.id"
                                :value="currency.id"
                            >
                                {{ currency.title }}
                            </option>
                        </select>
                        <div
                            v-if="form.errors.currency_id"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.currency_id }}
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700"
                            >Amount</label
                        >
                        <input
                            v-model="form.amount"
                            type="number"
                            step="0.00000001"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        />
                        <div
                            v-if="form.errors.amount"
                            class="mt-1 text-sm text-red-600"
                        >
                            {{ form.errors.amount }}
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md"
                    >
                        Create Deposit
                    </button>
                </form>
            </div>

            <!-- Список депозитов -->
            <div class="mt-8">
                <h2 class="text-lg font-medium text-gray-900">
                    Deposits History
                </h2>
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Currency</th>
                                <th>Payment System</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="deposit in deposits.data"
                                :key="deposit.uuid"
                            >
                                <td>{{ deposit.created_at }}</td>
                                <td>{{ deposit.amount }}</td>
                                <td>{{ deposit.currency.code }}</td>
                                <td>{{ deposit.payment_system.title }}</td>
                                <td>{{ deposit.status }}</td>
                                <td>
                                    <Link
                                        :href="
                                            route(
                                                'deposit.show',
                                                deposit.uuid
                                            )
                                        "
                                    >
                                        View
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AccountLayout>
</template>

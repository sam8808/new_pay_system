<!-- resources/js/Pages/Account/Deposits/Show.vue -->
<script setup>

const props = defineProps({
    deposit: Object,
});
</script>

<template>
    <AccountLayout>
        <div class="p-4 sm:p-8 bg-white shadow">
            <div class="max-w-xl">
                <h2 class="text-lg font-medium text-gray-900">
                    Deposit Details
                </h2>

                <dl class="mt-6 space-y-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Status
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ deposit.status }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Amount
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ deposit.amount }} {{ deposit.currency.code }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Processing Fee
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ deposit.processing_fee }}
                            {{ deposit.currency.code }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Payment System
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ deposit.payment_system.title }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-gray-500">
                            Created At
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ deposit.created_at }}
                        </dd>
                    </div>

                    <div v-if="deposit.expires_at">
                        <dt class="text-sm font-medium text-gray-500">
                            Expires At
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900">
                            {{ deposit.expires_at }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </AccountLayout>
</template>

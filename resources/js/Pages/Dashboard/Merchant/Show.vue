<script setup>
import { ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import {
    ArrowLeft,
    Store,
    Edit,
    Trash2,
    CheckCircle,
    XCircle,
    AlertCircle,
    Ban,
    Power,
} from "lucide-vue-next";

const props = defineProps({
    merchant: {
        type: Object,
        required: true,
    },
});

const deleteForm = useForm({});
const activateForm = useForm({});

const handleDelete = () => {
    if (confirm("Вы уверены, что хотите удалить этот магазин?")) {
        deleteForm.post(route("merchant.destroy", props.merchant.id));
    }
};

const handleActivation = () => {
    activateForm.post(route("merchant.activate", props.merchant.id));
};

const getMerchantStatus = (merchant) => {
    if (merchant.banned) {
        return {
            icon: Ban,
            class: "bg-red-50 text-red-700",
            text: "Заблокировано",
        };
    }
    if (merchant.rejected) {
        return {
            icon: XCircle,
            class: "bg-red-50 text-red-700",
            text: "Отказано",
        };
    }
    if (!merchant.approved) {
        return {
            icon: AlertCircle,
            class: "bg-yellow-50 text-yellow-700",
            text: "На модерации",
        };
    }
    if (merchant.activated) {
        return {
            icon: CheckCircle,
            class: "bg-emerald-50 text-emerald-700",
            text: "Активный",
        };
    }
    return {
        icon: Power,
        class: "bg-gray-50 text-gray-700",
        text: "Неактивный",
    };
};
</script>

<template>
    <DashboardLayout>
        <div class="container mx-auto px-6 py-8">
            <!-- Заголовок -->
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8"
            >
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-2xl font-medium text-gray-900">
                            Магазин -
                            <span class="text-violet-600">{{
                                merchant.m_id
                            }}</span>
                        </h1>
                        <div
                            :class="[
                                'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium gap-1.5',
                                getMerchantStatus(merchant).class,
                            ]"
                        >
                            <component
                                :is="getMerchantStatus(merchant).icon"
                                class="w-4 h-4"
                            />
                            {{ getMerchantStatus(merchant).text }}
                        </div>
                    </div>
                    <p class="text-sm text-gray-500">{{ merchant.title }}</p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('merchant')"
                        class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm font-medium rounded-xl text-gray-600 hover:bg-gray-50 transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Назад
                    </Link>

                    <Link
                        :href="route('merchant.edit', merchant.id)"
                        class="inline-flex items-center px-3 py-2 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 transition-colors"
                    >
                        <Edit class="w-4 h-4 mr-2" />
                        Редактировать
                    </Link>

                    <button
                        @click="handleDelete"
                        class="inline-flex items-center px-3 py-2 border border-red-200 text-sm font-medium rounded-xl text-red-600 hover:bg-red-50 transition-colors"
                    >
                        <Trash2 class="w-4 h-4 mr-2" />
                        Удалить
                    </button>
                </div>
            </div>

            <!-- Основная информация -->
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <!-- Основные данные -->
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-500 mb-1"
                                    >Название</label
                                >
                                <div
                                    class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg"
                                >
                                    {{ merchant.title }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-500 mb-1"
                                    >ID</label
                                >
                                <div
                                    class="text-sm font-medium text-violet-600 bg-gray-50 p-3 rounded-lg"
                                >
                                    {{ merchant.m_id }}
                                </div>
                            </div>
                        </div>

                        <!-- Технические данные -->
                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-500 mb-1"
                                    >Домен</label
                                >
                                <div
                                    class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg"
                                >
                                    {{ merchant.base_url }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-500 mb-1"
                                    >Ключ</label
                                >
                                <div
                                    class="text-sm font-mono text-gray-900 bg-gray-50 p-3 rounded-lg"
                                >
                                    {{ merchant.m_key }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- URLs -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >URL успешной оплаты</label
                            >
                            <div
                                class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg"
                            >
                                {{ merchant.success_url }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >URL неуспешной оплаты</label
                            >
                            <div
                                class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg"
                            >
                                {{ merchant.fail_url }}
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-xs font-medium text-gray-500 mb-1"
                                >URL обработчика</label
                            >
                            <div
                                class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg"
                            >
                                {{ merchant.handler_url }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Действия -->
                <div
                    class="flex justify-center p-6 bg-gray-50 border-t border-gray-100"
                >
                    <div
                        v-if="merchant.rejected || merchant.banned"
                        :class="[
                            'inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium gap-2',
                            merchant.rejected
                                ? 'bg-red-50 text-red-700'
                                : 'bg-red-50 text-red-700',
                        ]"
                    >
                        <component
                            :is="merchant.rejected ? XCircle : Ban"
                            class="w-4 h-4"
                        />
                        {{ merchant.rejected ? "Отказано" : "Заблокировано" }}
                    </div>

                    <button
                        v-else-if="merchant.approved"
                        @click="handleActivation"
                        :class="[
                            'inline-flex items-center px-4 py-2 rounded-xl text-sm font-medium gap-2 transition-colors',
                            merchant.activated
                                ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
                        ]"
                    >
                        <Power class="w-4 h-4" />
                        {{ merchant.activated ? "Отключить" : "Активировать" }}
                    </button>

                    <div
                        v-else
                        class="inline-flex items-center px-4 py-2 bg-yellow-50 text-yellow-700 rounded-xl text-sm font-medium gap-2"
                    >
                        <AlertCircle class="w-4 h-4" />
                        На модерации
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

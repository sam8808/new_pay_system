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
    Check
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
    if (merchant.is_active) {
        return {
            icon: Check,
            class: "bg-emerald-50 text-emerald-700",
            text: "Подключен",
        };
    }
    return {
        icon: AlertCircle,
        class: "bg-yellow-50 text-yellow-700",
        text: "Отключен",
    };
};
</script>

<template>
    <AccountLayout>
        <div class="container mx-auto px-8 py-8">
            <!-- Заголовок -->
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8"
            >
                <div class="space-y-3">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-12 h-12 bg-violet-50 rounded-xl flex items-center justify-center"
                        >
                            <Store class="w-6 h-6 text-violet-600" />
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h1
                                    class="text-2xl font-semibold text-gray-900"
                                >
                                    Магазин -
                                    <span class="text-violet-600">{{
                                        merchant.merchant_id
                                    }}</span>
                                </h1>
                                <div
                                    :class="[
                                        'inline-flex items-center px-3 py-1.5 rounded-full text-xs font-medium gap-1.5',
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
                            <p class="text-sm text-gray-500 mt-1">
                                {{ merchant.title }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('merchant')"
                        class="inline-flex items-center px-4 py-2.5 border border-gray-200 text-sm font-medium rounded-xl text-gray-600 hover:bg-gray-50 transition-all duration-300"
                    >
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        Назад
                    </Link>

                    <Link
                        :href="route('merchant.edit', merchant.id)"
                        class="inline-flex items-center px-4 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 shadow-lg shadow-violet-600/20 transition-all duration-300"
                    >
                        <Edit class="w-4 h-4 mr-2" />
                        Редактировать
                    </Link>

                    <button
                        @click="handleDelete"
                        class="inline-flex items-center px-4 py-2.5 border border-red-200 text-sm font-medium rounded-xl text-red-600 hover:bg-red-50 transition-all duration-300"
                    >
                        <Trash2 class="w-4 h-4 mr-2" />
                        Удалить
                    </button>
                </div>
            </div>

            <!-- Основная информация -->
            <div
                class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden mb-6 backdrop-blur-xl"
            >
                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Основные данные -->
                        <div class="space-y-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Название</label
                                >
                                <div
                                    class="text-sm text-gray-900 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100"
                                >
                                    {{ merchant.title }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >ID</label
                                >
                                <div
                                    class="text-sm font-medium text-violet-600 bg-violet-50 px-4 py-3 rounded-xl"
                                >
                                    {{ merchant.merchant_id }}
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >Домен</label
                                >
                                <div
                                    class="text-sm text-gray-900 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100"
                                >
                                    {{ merchant.domain }}
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                    >URL WebHook</label
                                >
                                <div
                                    class="text-sm font-mono text-gray-900 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100"
                                >
                                    {{ merchant.webhook_url }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >API Ключ</label
                            >
                            <div
                                class="text-sm font-mono text-gray-900 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100"
                            >
                                {{ merchant.api_key }}
                            </div>
                        </div>
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                                >Секретный Ключ</label
                            >
                            <div
                                class="text-sm font-mono text-gray-900 bg-gray-50 px-4 py-3 rounded-xl border border-gray-100"
                            >
                                {{ merchant.secret_key }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Действия -->
                <!-- <div
                    class="flex justify-center p-6 bg-gray-50/80 border-t border-gray-100 backdrop-blur-sm"
                >
                    <div
                        v-if="merchant.rejected || merchant.banned"
                        :class="[
                            'inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-medium gap-2',
                            merchant.rejected
                                ? 'bg-red-50 text-red-700'
                                : 'bg-red-50 text-red-700',
                        ]"
                    >
                        <component
                            :is="!merchant.is_active ? XCircle : Ban"
                            class="w-4 h-4"
                        />
                        {{ merchant.rejected ? "Отказано" : "Заблокировано" }}
                    </div>

                    <button
                        v-else-if="merchant.approved"
                        @click="handleActivation"
                        :class="[
                            'inline-flex items-center px-5 py-2.5 rounded-xl text-sm font-medium gap-2 transition-all duration-300',
                            merchant.is_active
                                ? 'bg-red-50 text-red-700 hover:bg-red-100'
                                : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
                        ]"
                    >
                        <Power class="w-4 h-4" />
                        {{ merchant.is_active ? "Отключить" : "Активировать" }}
                    </button>

                    <div
                        v-else
                        class="inline-flex items-center px-5 py-2.5 bg-yellow-50 text-yellow-700 rounded-xl text-sm font-medium gap-2"
                    >
                        <AlertCircle class="w-4 h-4" />
                        На модерации
                    </div>
                </div> -->
            </div>
        </div>
    </AccountLayout>
</template>

<style scoped>
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

.backdrop-blur-sm {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}
</style>

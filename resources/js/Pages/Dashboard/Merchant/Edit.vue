<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import {
    ArrowLeft,
    Store,
    Save,
    Power,
    CheckCircle,
    XCircle,
} from "lucide-vue-next";

const props = defineProps({
    merchant: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    title: props.merchant.title,
    m_key: props.merchant.m_key,
    success_url: props.merchant.success_url,
    fail_url: props.merchant.fail_url,
    handler_url: props.merchant.handler_url,
});

const submit = () => {
    form.post(route("merchant.update", props.merchant.id));
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
                            Редактирование магазина -
                            <span class="text-violet-600">{{
                                merchant.m_id
                            }}</span>
                        </h1>

                        <div
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium"
                            :class="
                                merchant.activated
                                    ? 'bg-emerald-50 text-emerald-700'
                                    : 'bg-red-50 text-red-700'
                            "
                        >
                            <component
                                :is="merchant.activated ? CheckCircle : XCircle"
                                class="w-4 h-4"
                            />
                            {{ merchant.activated ? "Активный" : "Неактивный" }}
                        </div>
                    </div>
                    <p class="text-sm text-gray-500">{{ merchant.base_url }}</p>
                </div>

                <Link
                    :href="route('merchant')"
                    class="inline-flex items-center px-3 py-2 border border-gray-200 text-sm font-medium rounded-xl text-gray-600 hover:bg-gray-50 transition-colors"
                >
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Назад
                </Link>
            </div>

            <!-- Форма редактирования -->
            <form
                @submit.prevent="submit"
                class="bg-white rounded-2xl shadow-sm overflow-hidden"
            >
                <div class="p-6 space-y-6">
                    <!-- Основная информация -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Название -->
                        <div>
                            <label
                                for="title"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Название
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                            />
                            <p
                                v-if="form.errors.title"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Домен (disabled) -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Домен
                            </label>
                            <input
                                type="text"
                                :value="merchant.base_url"
                                disabled
                                class="w-full px-4 py-2.5 text-sm bg-gray-100 border border-gray-200 rounded-xl text-gray-500"
                            />
                        </div>
                    </div>

                    <!-- ID и Ключ -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- ID (disabled) -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                ID
                            </label>
                            <input
                                type="text"
                                :value="merchant.m_id"
                                disabled
                                class="w-full px-4 py-2.5 text-sm bg-gray-100 border border-gray-200 rounded-xl text-gray-500"
                            />
                        </div>

                        <!-- Ключ -->
                        <div>
                            <label
                                for="m_key"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Ключ
                            </label>
                            <input
                                id="m_key"
                                v-model="form.m_key"
                                type="text"
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors font-mono"
                            />
                            <p
                                v-if="form.errors.m_key"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.m_key }}
                            </p>
                        </div>
                    </div>

                    <!-- URLs -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- URL успешной оплаты -->
                        <div>
                            <label
                                for="success_url"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                URL успешной оплаты
                            </label>
                            <input
                                id="success_url"
                                v-model="form.success_url"
                                type="text"
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                            />
                            <p
                                v-if="form.errors.success_url"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.success_url }}
                            </p>
                        </div>

                        <!-- URL неудачной оплаты -->
                        <div>
                            <label
                                for="fail_url"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                URL неудачной оплаты
                            </label>
                            <input
                                id="fail_url"
                                v-model="form.fail_url"
                                type="text"
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                            />
                            <p
                                v-if="form.errors.fail_url"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.fail_url }}
                            </p>
                        </div>

                        <!-- URL обработчика -->
                        <div>
                            <label
                                for="handler_url"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                URL обработчика
                            </label>
                            <input
                                id="handler_url"
                                v-model="form.handler_url"
                                type="text"
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                            />
                            <p
                                v-if="form.errors.handler_url"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.handler_url }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Кнопка сохранения -->
                <div
                    class="flex justify-center p-6 bg-gray-50 border-t border-gray-100"
                >
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center px-4 py-2.5 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Save class="w-4 h-4 mr-2" />
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>

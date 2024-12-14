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
});

const submit = () => {
    form.post(route("merchant.update", props.merchant.id));
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
                                    Редактирование магазина -
                                    <span class="text-violet-600">{{
                                        merchant.m_id
                                    }}</span>
                                </h1>
                                <div
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium"
                                    :class="
                                        merchant.activated
                                            ? 'bg-emerald-50 text-emerald-700'
                                            : 'bg-red-50 text-red-700'
                                    "
                                >
                                    <component
                                        :is="
                                            merchant.activated
                                                ? CheckCircle
                                                : XCircle
                                        "
                                        class="w-4 h-4"
                                    />
                                    {{
                                        merchant.activated
                                            ? "Активный"
                                            : "Неактивный"
                                    }}
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ merchant.webhook_url }}
                            </p>
                        </div>
                    </div>
                </div>

                <Link
                    :href="route('merchant')"
                    class="inline-flex items-center px-4 py-2.5 border border-gray-200 text-sm font-medium rounded-xl text-gray-600 hover:bg-gray-50 transition-all duration-300"
                >
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Назад
                </Link>
            </div>

            <!-- Форма редактирования -->
            <form
                @submit.prevent="submit"
                class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden backdrop-blur-xl"
            >
                <div class="p-8 space-y-8">
                    <!-- Основная информация -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Название -->
                        <div class="space-y-2">
                            <label
                                for="title"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Название
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300"
                            />
                            <p
                                v-if="form.errors.title"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.title }}
                            </p>
                        </div>

                        <!-- Домен (disabled) -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                            >
                                Домен
                            </label>
                            <input
                                type="text"
                                :value="merchant.webhook_url"
                                disabled
                                class="w-full px-4 py-3 text-sm bg-gray-100 border border-gray-200 rounded-xl text-gray-500"
                            />
                        </div>
                    </div>

                    <!-- ID и Ключ -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- ID (disabled) -->
                        <div class="space-y-2">
                            <label
                                class="block text-sm font-medium text-gray-700"
                            >
                                ID
                            </label>
                            <input
                                type="text"
                                :value="merchant.m_id"
                                disabled
                                class="w-full px-4 py-3 text-sm bg-gray-100 border border-gray-200 rounded-xl text-gray-500"
                            />
                        </div>

                        <!-- Ключ -->
                        <div class="space-y-2">
                            <label
                                for="m_key"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Ключ
                            </label>
                            <input
                                id="m_key"
                                v-model="form.m_key"
                                type="text"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300 font-mono"
                            />
                            <p
                                v-if="form.errors.m_key"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.m_key }}
                            </p>
                        </div>
                    </div>

                    <!-- URLs -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- URL успешной оплаты -->
                        <div class="space-y-2">
                            <label
                                for="success_url"
                                class="block text-sm font-medium text-gray-700"
                            >
                                URL успешной оплаты
                            </label>
                            <input
                                id="success_url"
                                v-model="form.success_url"
                                type="text"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300"
                            />
                            <p
                                v-if="form.errors.success_url"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.success_url }}
                            </p>
                        </div>

                        <!-- URL неудачной оплаты -->
                        <div class="space-y-2">
                            <label
                                for="fail_url"
                                class="block text-sm font-medium text-gray-700"
                            >
                                URL неудачной оплаты
                            </label>
                            <input
                                id="fail_url"
                                v-model="form.fail_url"
                                type="text"
                                class="w-full px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300"
                            />
                            <p
                                v-if="form.errors.fail_url"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.fail_url }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Кнопка сохранения -->
                <div
                    class="flex justify-center p-6 bg-gray-50/80 border-t border-gray-100 backdrop-blur-sm"
                >
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center px-6 py-3 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 shadow-lg shadow-violet-600/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none"
                    >
                        <Save class="w-4 h-4 mr-2" />
                        Сохранить изменения
                    </button>
                </div>
            </form>
        </div>
    </AccountLayout>
</template>

<style scoped>
/* Улучшенные эффекты */
.shadow-lg {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

/* Стилизация полей ввода при фокусе */
input:focus {
    box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
}
</style>

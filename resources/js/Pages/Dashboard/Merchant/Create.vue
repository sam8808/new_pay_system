<script setup>
import { useForm } from "@inertiajs/vue3";
import {
    ArrowLeft,
    Store,
    Link as LinkIcon,
    Globe,
    CheckCircle,
    AlertCircle,
    Plus,
} from "lucide-vue-next";

// Инициализация формы
const form = useForm({
    title: "",
    domain: "",
    webhook_url: "",
    allowed_ips: "",
});

// Обработка отправки формы
const submit = () => {
    form.post(route("merchant.store"), {
        onSuccess: () => {
            // Очистить форму после успешной отправки
            form.reset();
        },
        onError: () => {
            // Логировать ошибки, если необходимо
            console.error(form.errors);
        },
    });
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
                            :href="route('merchant')"
                            class="flex items-center justify-center w-11 h-11 rounded-xl border border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-all duration-300"
                        >
                            <ArrowLeft class="w-5 h-5" />
                        </Link>
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-900">
                                Подключение магазина
                            </h1>
                            <p class="text-sm text-gray-500 mt-1">
                                Заполните форму для добавления нового магазина
                            </p>
                        </div>
                    </div>
                    <div class="hidden sm:block">
                        <Link
                            :href="route('merchant')"
                            class="inline-flex items-center px-5 py-2.5 text-sm text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all duration-300"
                        >
                            Отменить
                        </Link>
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
                            Информация о магазине
                        </h2>
                    </div>
                    <div class="p-8 space-y-8">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label
                                    for="title"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Название магазина
                                </label>
                                <div class="relative">
                                    <Store
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    />
                                    <input
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="w-full pl-12 pr-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300"
                                        placeholder="Введите название магазина"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.title"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.title }}
                                </p>
                            </div>
                            <div class="space-y-2">
                                <label
                                    for="domain"
                                    class="block text-sm font-medium text-gray-700"
                                >
                                    Домен
                                </label>
                                <div class="relative">
                                    <Globe
                                        class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    />
                                    <input
                                        id="domain"
                                        v-model="form.domain"
                                        type="text"
                                        class="w-full pl-12 pr-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300"
                                        placeholder="https://"
                                    />
                                </div>
                                <p
                                    class="text-sm text-gray-500 flex items-center gap-1.5"
                                >
                                    <AlertCircle
                                        class="w-4 h-4 text-gray-400"
                                    />
                                    Убедитесь, что домен использует HTTPS
                                </p>
                                <p
                                    v-if="form.errors.domain"
                                    class="text-sm text-red-600"
                                >
                                    {{ form.errors.domain }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Webhook URL -->
                <div
                    class="bg-white rounded-2xl shadow-lg shadow-gray-100/50 overflow-hidden backdrop-blur-xl"
                >
                    <div class="px-8 py-5 border-b border-gray-100">
                        <h2 class="font-medium text-gray-900">
                            Настройка Webhook URL
                        </h2>
                    </div>
                    <div class="p-8">
                        <div class="space-y-2">
                            <label
                                for="webhook_url"
                                class="block text-sm font-medium text-gray-700"
                            >
                                Webhook URL
                            </label>
                            <div class="relative">
                                <CheckCircle
                                    class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                />
                                <input
                                    id="webhook_url"
                                    v-model="form.webhook_url"
                                    type="text"
                                    class="w-full pl-12 pr-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-all duration-300"
                                    placeholder="https://your-domain.com/webhook"
                                />
                            </div>
                            <p
                                v-if="form.errors.webhook_url"
                                class="text-sm text-red-600"
                            >
                                {{ form.errors.webhook_url }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Кнопки -->
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
                        class="inline-flex items-center px-5 py-3 bg-violet-600 text-white text-sm font-medium rounded-xl hover:bg-violet-700 shadow-lg shadow-violet-600/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-violet-500 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <Plus class="w-4 h-4 mr-2" />
                        Подключить магазин
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

/* Стилизация полей ввода при фокусе */
input:focus {
    box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.1);
}
</style>

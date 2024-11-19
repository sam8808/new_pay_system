<script setup>
import { ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import {
    ArrowLeft,
    Store,
    Link as LinkIcon,
    Globe,
    CheckCircle,
    AlertCircle,
    Plus,
} from "lucide-vue-next";

const form = useForm({
    title: "",
    base_url: "",
    success_url: "",
    fail_url: "",
    handler_url: "",
});

const submit = () => {
    form.post(route("merchant.store"));
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
                            :href="route('merchant')"
                            class="flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200 text-gray-400 hover:text-gray-600 hover:bg-gray-50 transition-colors"
                        >
                            <ArrowLeft class="w-5 h-5" />
                        </Link>
                        <div>
                            <h1 class="text-2xl font-medium text-gray-900">
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
                            class="inline-flex items-center px-4 py-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors"
                        >
                            Отменить
                        </Link>
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
                            Информация о магазине
                        </h2>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Название и домен -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label
                                    for="title"
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Название магазина
                                </label>
                                <div class="relative">
                                    <Store
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    />
                                    <input
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                                        placeholder="Введите название магазина"
                                    />
                                </div>
                                <p
                                    v-if="form.errors.title"
                                    class="mt-1.5 text-sm text-red-600"
                                >
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <div>
                                <label
                                    for="base_url"
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Домен
                                </label>
                                <div class="relative">
                                    <Globe
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                    />
                                    <input
                                        id="base_url"
                                        v-model="form.base_url"
                                        type="text"
                                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                                        placeholder="https://"
                                    />
                                </div>
                                <p
                                    class="mt-1.5 text-sm text-gray-500 flex items-center gap-1.5"
                                >
                                    <AlertCircle
                                        class="w-4 h-4 text-gray-400"
                                    />
                                    Убедитесь, что домен использует протокол
                                    HTTPS
                                </p>
                                <p
                                    v-if="form.errors.base_url"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.base_url }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- URL настройки -->
                <div
                    class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6"
                >
                    <div class="px-6 py-4 border-b border-gray-100">
                        <div class="flex justify-between items-center">
                            <h2 class="font-medium text-gray-900">
                                Настройка URL-адресов
                            </h2>
                            <span class="text-xs text-gray-500"
                                >Все поля обязательны</span
                            >
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Success URL -->
                        <div>
                            <label
                                for="success_url"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                URL успешной оплаты
                            </label>
                            <div class="relative">
                                <CheckCircle
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                />
                                <input
                                    id="success_url"
                                    v-model="form.success_url"
                                    type="text"
                                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                                    placeholder="https://your-domain.com/payment/success"
                                />
                            </div>
                            <p
                                v-if="form.errors.success_url"
                                class="mt-1.5 text-sm text-red-600"
                            >
                                {{ form.errors.success_url }}
                            </p>
                        </div>

                        <!-- Fail URL -->
                        <div>
                            <label
                                for="fail_url"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                URL неуспешной оплаты
                            </label>
                            <div class="relative">
                                <AlertCircle
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                />
                                <input
                                    id="fail_url"
                                    v-model="form.fail_url"
                                    type="text"
                                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                                    placeholder="https://your-domain.com/payment/fail"
                                />
                            </div>
                            <p
                                v-if="form.errors.fail_url"
                                class="mt-1.5 text-sm text-red-600"
                            >
                                {{ form.errors.fail_url }}
                            </p>
                        </div>

                        <!-- Handler URL -->
                        <div>
                            <label
                                for="handler_url"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                URL обработчика
                            </label>
                            <div class="relative">
                                <LinkIcon
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                                />
                                <input
                                    id="handler_url"
                                    v-model="form.handler_url"
                                    type="text"
                                    class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors"
                                    placeholder="https://your-domain.com/payment/handler"
                                />
                            </div>
                            <p
                                v-if="form.errors.handler_url"
                                class="mt-1.5 text-sm text-red-600"
                            >
                                {{ form.errors.handler_url }}
                            </p>
                        </div>
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
                        <Plus class="w-4 h-4 mr-2" />
                        Подключить магазин
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>

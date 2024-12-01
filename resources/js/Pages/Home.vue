<script setup>
import { ref, onMounted, onUnmounted, defineProps } from "vue";

// Состояния
const isMobileMenuOpen = ref(false);
const isScrolled = ref(false);
const isVisible = ref(false);
const isFeaturesVisible = ref(false);
const isPricingVisible = ref(false);
const isSupportVisible = ref(false);
const isSubmitting = ref(false);

defineProps({
    title: String,
});

// Форма и ошибки
const form = ref({
    name: "",
    email: "",
    message: "",
});

const formErrors = ref({});

// Навигация
const navigationLinks = [
    { href: "#features", text: "Возможности" },
    { href: "#pricing", text: "Тарифы" },
    { href: "#support", text: "Поддержка" },
];

// Особенности
const features = [
    {
        title: "Безопасность",
        description:
            "Защита данных по международным стандартам PCI DSS и шифрование всех транзакций",
        icon: "M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z",
        bgColor: "bg-blue-100",
        iconColor: "text-blue-600",
    },
    {
        title: "Быстрые переводы",
        description:
            "Мгновенные платежи и переводы между пользователями системы 24/7",
        icon: "M13 10V3L4 14h7v7l9-11h-7z",
        bgColor: "bg-indigo-100",
        iconColor: "text-indigo-600",
    },
    {
        title: "Аналитика",
        description:
            "Детальная статистика и отчеты по всем операциям в реальном времени",
        icon: "M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z",
        bgColor: "bg-violet-100",
        iconColor: "text-violet-600",
    },
];

// Тарифные планы
const pricingPlans = [
    {
        name: "Старт",
        price: "0₽",
        features: ["До 100 транзакций", "Базовая аналитика", "Email поддержка"],
        buttonText: "Выбрать",
        popular: false,
    },
    {
        name: "Бизнес",
        price: "2999₽",
        features: [
            "Безлимитные транзакции",
            "Расширенная аналитика",
            "24/7 поддержка",
            "API интеграция",
        ],
        buttonText: "Выбрать",
        popular: true,
    },
    {
        name: "Корпоративный",
        price: "9999₽",
        features: [
            "Все функции Бизнес",
            "Персональный менеджер",
            "Индивидуальные условия",
        ],
        buttonText: "Связаться",
        popular: false,
    },
];

// Контакты
const contacts = [
    {
        title: "Email поддержка",
        value: "support@paysystem.ru",
        icon: "M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z",
    },
    {
        title: "Телефон",
        value: "8 800 100-20-30",
        icon: "M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z",
    },
];

// Социальные сети
const socialLinks = [
    {
        href: "#",
        icon: "M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22",
    },
    {
        href: "#",
        icon: "M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z",
    },
    {
        href: "#",
        icon: "M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z M2 9h4v12H2z M4 6a2 2 0 1 1-4 0 2 2 0 0 1 4 0z",
    },
];

// Колонки футера
const footerColumns = [
    {
        title: "Продукт",
        links: [
            { text: "Возможности", href: "#features" },
            { text: "Тарифы", href: "#pricing" },
            { text: "API", href: "#", external: true },
            { text: "Документация", href: "#" },
        ],
    },
    {
        title: "Компания",
        links: [
            { text: "О нас", href: "#" },
            { text: "Блог", href: "#" },
            { text: "Карьера", href: "#" },
            { text: "Контакты", href: "#" },
        ],
    },
    {
        title: "Безопасность",
        links: [
            { text: "Центр безопасности", href: "#" },
            { text: "PCI DSS", href: "#", external: true },
            { text: "SSL Сертификация", href: "#" },
            { text: "Правовая информация", href: "#" },
        ],
    },
];

// Правовые ссылки
const legalLinks = [
    { text: "Условия использования", href: "#" },
    { text: "Конфиденциальность", href: "#" },
];

// Методы
const handleScroll = () => {
    isScrolled.value = window.scrollY > 0;
};

const smoothScroll = (target) => {
    const element = document.querySelector(target);
    if (element) {
        element.scrollIntoView({ behavior: "smooth" });
    }
    isMobileMenuOpen.value = false;
};

const handleSubmit = async () => {
    formErrors.value = {};
    isSubmitting.value = true;

    // Валидация
    if (!form.value.name) formErrors.value.name = "Введите ваше имя";
    if (!form.value.email) formErrors.value.email = "Введите email";
    if (!form.value.message) formErrors.value.message = "Введите сообщение";

    if (Object.keys(formErrors.value).length === 0) {
        try {
            await new Promise((resolve) => setTimeout(resolve, 1500)); // Имитация запроса
            form.value = { name: "", email: "", message: "" };
            // Здесь можно добавить уведомление об успешной отправке
        } catch (error) {
            // Обработка ошибок
        }
    }

    isSubmitting.value = false;
};

// Наблюдатель за видимостью секций
const createIntersectionObserver = (callback) => {
    return new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    callback(true);
                }
            });
        },
        { threshold: 0.1 }
    );
};

onMounted(() => {
    window.addEventListener("scroll", handleScroll);

    // Инициализация наблюдателей
    const heroSection = document.querySelector("section");
    const featuresSection = document.querySelector("#features");
    const pricingSection = document.querySelector("#pricing");
    const supportSection = document.querySelector("#support");

    if (heroSection) {
        createIntersectionObserver(() => (isVisible.value = true)).observe(
            heroSection
        );
    }
    if (featuresSection) {
        createIntersectionObserver(
            () => (isFeaturesVisible.value = true)
        ).observe(featuresSection);
    }
    if (pricingSection) {
        createIntersectionObserver(
            () => (isPricingVisible.value = true)
        ).observe(pricingSection);
    }
    if (supportSection) {
        createIntersectionObserver(
            () => (isSupportVisible.value = true)
        ).observe(supportSection);
    }
});

onUnmounted(() => {
    window.removeEventListener("scroll", handleScroll);
});
</script>

<template>
    <Head :title="title" />
    <div
        class="min-h-screen bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-100 via-violet-100 to-gray-100"
    >
        <!-- Header с анимацией при скролле -->
        <header
            class="fixed w-full z-50 transition-all duration-300"
            :class="{ 'bg-white/80 backdrop-blur-sm shadow-lg': isScrolled }"
        >
            <nav class="container mx-auto px-6 py-4">
                <div class="flex items-center justify-between">
                    <Link :href="route('home')" class="group">
                        <div class="flex items-center space-x-2">
                            <svg
                                class="w-8 h-8 text-blue-600 transform transition-transform group-hover:rotate-12"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                />
                            </svg>
                            <span
                                class="text-xl font-bold bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 bg-clip-text text-transparent"
                                >PaySystem</span
                            >
                        </div>
                    </Link>

                    <div class="hidden md:flex items-center space-x-8">
                        <template
                            v-for="(link, index) in navigationLinks"
                            :key="index"
                        >
                            <a
                                :href="link.href"
                                class="relative text-gray-600 hover:text-blue-600 transition-colors group"
                                @click="smoothScroll(link.href)"
                            >
                                {{ link.text }}
                                <span
                                    class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"
                                ></span>
                            </a>
                        </template>
                        <Link
                            :href="route('login')"
                            class="relative text-gray-600 hover:text-blue-600 transition-colors group"
                        >
                            Войти
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-blue-600 transition-all group-hover:w-full"
                            ></span>
                        </Link>
                        <Link
                            :href="route('register')"
                            class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 text-white px-6 py-2 rounded-xl group"
                        >
                            <span class="relative z-10">Регистрация</span>
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-violet-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity"
                            ></div>
                        </Link>
                    </div>

                    <!-- Улучшенная мобильная кнопка -->
                    <button
                        class="md:hidden relative w-10 h-10 text-gray-600 focus:outline-none group"
                        @click="toggleMobileMenu"
                    >
                        <div
                            class="absolute inset-0 flex items-center justify-center"
                        >
                            <div
                                class="w-6 transform transition-transform duration-300"
                                :class="{ 'rotate-180': isMobileMenuOpen }"
                            >
                                <div
                                    class="w-full h-px bg-current transform transition-all duration-300"
                                    :class="{
                                        'rotate-45 translate-y-px':
                                            isMobileMenuOpen,
                                    }"
                                ></div>
                                <div
                                    class="w-full h-px bg-current mt-2 transform transition-all duration-300"
                                    :class="{
                                        '-rotate-45 -translate-y-px':
                                            isMobileMenuOpen,
                                    }"
                                ></div>
                            </div>
                        </div>
                    </button>
                </div>

                <!-- Анимированное мобильное меню -->
                <transition
                    enter-active-class="transition duration-200 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-150 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <div
                        v-if="isMobileMenuOpen"
                        class="md:hidden mt-4 p-4 bg-white/80 backdrop-blur-sm rounded-2xl border border-white/20"
                    >
                        <div class="space-y-4">
                            <template
                                v-for="(link, index) in navigationLinks"
                                :key="index"
                            >
                                <a
                                    :href="link.href"
                                    class="block text-gray-600 hover:text-blue-600 transform hover:translate-x-2 transition-all"
                                    @click="smoothScroll(link.href)"
                                >
                                    {{ link.text }}
                                </a>
                            </template>
                            <Link
                                :href="route('login')"
                                class="block text-gray-600 hover:text-blue-600 transform hover:translate-x-2 transition-all"
                            >
                                Войти
                            </Link>
                            <Link
                                :href="route('register')"
                                class="block text-blue-600 font-semibold transform hover:translate-x-2 transition-all"
                            >
                                Регистрация
                            </Link>
                        </div>
                    </div>
                </transition>
            </nav>
        </header>

        <!-- Hero Section с анимациями -->
        <section class="relative pt-32 pb-32">
            <!-- Анимированные декоративные элементы -->
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div
                    class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-r from-blue-400/30 to-indigo-400/30 rounded-full filter blur-3xl animate-pulse"
                    style="animation-duration: 4s"
                ></div>
                <div
                    class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-gradient-to-r from-violet-400/30 to-purple-400/30 rounded-full filter blur-3xl animate-pulse"
                    style="animation-duration: 6s"
                ></div>
            </div>

            <div class="container mx-auto px-6">
                <div class="max-w-4xl mx-auto text-center">
                    <h1
                        class="text-5xl font-bold mb-8 bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 bg-clip-text text-transparent transform transition-all duration-700"
                        :class="{
                            'translate-y-0 opacity-100': isVisible,
                            'translate-y-4 opacity-0': !isVisible,
                        }"
                    >
                        Безопасные платежи для вашего бизнеса
                    </h1>
                    <p
                        class="text-xl text-gray-600 mb-12 transform transition-all duration-700 delay-200"
                        :class="{
                            'translate-y-0 opacity-100': isVisible,
                            'translate-y-4 opacity-0': !isVisible,
                        }"
                    >
                        Принимайте платежи онлайн, управляйте финансами и
                        развивайте свой бизнес с помощью современной платежной
                        системы
                    </p>
                    <div
                        class="flex flex-col sm:flex-row justify-center gap-4 transform transition-all duration-700 delay-400"
                        :class="{
                            'translate-y-0 opacity-100': isVisible,
                            'translate-y-4 opacity-0': !isVisible,
                        }"
                    >
                        <Link
                            :href="route('register')"
                            class="group relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 text-white px-8 py-4 rounded-xl font-semibold"
                        >
                            <span class="relative z-10">Начать бесплатно</span>
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-violet-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity"
                            ></div>
                        </Link>
                        <a
                            href="#demo"
                            class="group relative overflow-hidden bg-white/80 backdrop-blur-sm text-gray-700 px-8 py-4 rounded-xl font-semibold border border-white/20"
                        >
                            <span class="relative z-10">Демо-версия</span>
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-gray-100 to-gray-200 opacity-0 group-hover:opacity-100 transition-opacity"
                            ></div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section с анимациями -->
        <section id="features" class="py-20 bg-white/50">
            <div class="container mx-auto px-6">
                <h2
                    class="text-3xl font-bold text-center mb-16 bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 bg-clip-text text-transparent transform transition-all duration-700"
                    :class="{
                        'translate-y-0 opacity-100': isFeaturesVisible,
                        'translate-y-4 opacity-0': !isFeaturesVisible,
                    }"
                >
                    Возможности платформы
                </h2>

                <div class="grid md:grid-cols-3 gap-8">
                    <template v-for="(feature, index) in features" :key="index">
                        <div
                            class="group bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-white/20 hover:border-blue-200 transition-all duration-300 transform hover:-translate-y-1"
                            :class="{
                                'translate-y-0 opacity-100': isFeaturesVisible,
                                'translate-y-4 opacity-0': !isFeaturesVisible,
                            }"
                            :style="{ 'transition-delay': `${index * 150}ms` }"
                        >
                            <div
                                :class="[
                                    'w-12 h-12 rounded-xl flex items-center justify-center mb-6 transition-all duration-300 group-hover:scale-110',
                                    feature.bgColor,
                                ]"
                            >
                                <svg
                                    class="w-6 h-6 transition-transform group-hover:scale-110"
                                    :class="feature.iconColor"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        :d="feature.icon"
                                    />
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-4">
                                {{ feature.title }}
                            </h3>
                            <p class="text-gray-600">
                                {{ feature.description }}
                            </p>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Pricing Section с улучшенными эффектами -->
        <section id="pricing" class="py-20">
            <div class="container mx-auto px-6">
                <h2
                    class="text-3xl font-bold text-center mb-16 bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 bg-clip-text text-transparent transform transition-all duration-700"
                    :class="{
                        'translate-y-0 opacity-100': isPricingVisible,
                        'translate-y-4 opacity-0': !isPricingVisible,
                    }"
                >
                    Тарифные планы
                </h2>

                <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                    <template
                        v-for="(plan, index) in pricingPlans"
                        :key="index"
                    >
                        <div
                            class="relative group transition-all duration-500"
                            :class="[
                                plan.popular ? 'transform scale-105 z-10' : '',
                                {
                                    'translate-y-0 opacity-100':
                                        isPricingVisible,
                                    'translate-y-4 opacity-0':
                                        !isPricingVisible,
                                },
                            ]"
                            :style="{ 'transition-delay': `${index * 150}ms` }"
                        >
                            <div
                                v-if="plan.popular"
                                class="absolute -top-4 left-1/2 transform -translate-x-1/2"
                            >
                                <span
                                    class="bg-gradient-to-r from-blue-600 to-violet-600 text-white text-sm font-semibold px-4 py-1 rounded-full"
                                >
                                    Популярный выбор
                                </span>
                            </div>

                            <div
                                :class="[
                                    'h-full p-8 rounded-2xl transition-all duration-300 backdrop-blur-sm',
                                    plan.popular
                                        ? 'bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 text-white'
                                        : 'bg-white/80 border border-white/20 hover:border-blue-200 hover:-translate-y-1',
                                ]"
                            >
                                <h3 class="text-xl font-semibold mb-4">
                                    {{ plan.name }}
                                </h3>
                                <div class="mb-6">
                                    <div class="flex items-baseline space-x-1">
                                        <span class="text-4xl font-bold">{{
                                            plan.price
                                        }}</span>
                                        <span
                                            :class="
                                                plan.popular
                                                    ? 'text-white/80'
                                                    : 'text-gray-500'
                                            "
                                            >/мес</span
                                        >
                                    </div>
                                </div>
                                <ul class="space-y-4 mb-8">
                                    <li
                                        v-for="feature in plan.features"
                                        :key="feature"
                                        class="flex items-center"
                                    >
                                        <svg
                                            class="w-5 h-5 mr-2 flex-shrink-0"
                                            :class="
                                                plan.popular
                                                    ? 'text-white'
                                                    : 'text-green-500'
                                            "
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>
                                        <span
                                            :class="
                                                plan.popular
                                                    ? 'text-white/90'
                                                    : ''
                                            "
                                            >{{ feature }}</span
                                        >
                                    </li>
                                </ul>
                                <button
                                    class="w-full relative overflow-hidden py-3 rounded-xl font-semibold transition-all duration-300 group"
                                    :class="
                                        plan.popular
                                            ? 'bg-white text-blue-600 hover:bg-white/90'
                                            : 'bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 text-white'
                                    "
                                >
                                    <span class="relative z-10">{{
                                        plan.buttonText
                                    }}</span>
                                    <div
                                        class="absolute inset-0 transform scale-x-0 transition-transform origin-left group-hover:scale-x-100"
                                        :class="
                                            plan.popular
                                                ? 'bg-white/90'
                                                : 'bg-gradient-to-r from-indigo-600 via-violet-600 to-blue-600'
                                        "
                                    ></div>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Support Section с улучшенной формой -->
        <section id="support" class="py-20 bg-white/50">
            <div class="container mx-auto px-6">
                <h2
                    class="text-3xl font-bold text-center mb-16 bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 bg-clip-text text-transparent transform transition-all duration-700"
                    :class="{
                        'translate-y-0 opacity-100': isSupportVisible,
                        'translate-y-4 opacity-0': !isSupportVisible,
                    }"
                >
                    Поддержка 24/7
                </h2>

                <div class="grid md:grid-cols-2 gap-12 max-w-4xl mx-auto">
                    <!-- Support Info с анимацией -->
                    <div
                        class="space-y-6 transform transition-all duration-700"
                        :class="{
                            'translate-x-0 opacity-100': isSupportVisible,
                            '-translate-x-4 opacity-0': !isSupportVisible,
                        }"
                    >
                        <h3 class="text-2xl font-semibold mb-4">
                            Мы всегда на связи
                        </h3>
                        <p class="text-gray-600">
                            Наша команда поддержки готова помочь вам в любое
                            время дня и ночи. Выберите удобный способ связи:
                        </p>
                        <ul class="space-y-4">
                            <template
                                v-for="(contact, index) in contacts"
                                :key="index"
                            >
                                <li
                                    class="flex items-center space-x-3 transform transition-all"
                                    :class="{
                                        'translate-x-0 opacity-100':
                                            isSupportVisible,
                                        '-translate-x-4 opacity-0':
                                            !isSupportVisible,
                                    }"
                                    :style="{
                                        'transition-delay': `${index * 150}ms`,
                                    }"
                                >
                                    <div
                                        class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors"
                                    >
                                        <svg
                                            class="w-5 h-5 text-blue-600 transform group-hover:scale-110 transition-transform"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                :d="contact.icon"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold">
                                            {{ contact.title }}
                                        </h4>
                                        <p class="text-gray-600">
                                            {{ contact.value }}
                                        </p>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Улучшенная контактная форма -->
                    <div
                        class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl border border-white/20 transform transition-all duration-700"
                        :class="{
                            'translate-x-0 opacity-100': isSupportVisible,
                            'translate-x-4 opacity-0': !isSupportVisible,
                        }"
                    >
                        <form class="space-y-6" @submit.prevent="handleSubmit">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold"
                                    >Ваше имя</label
                                >
                                <div class="relative">
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        class="w-full h-12 px-4 pl-10 bg-white/50 border rounded-xl transition-all duration-300 focus:ring-4"
                                        :class="[
                                            'border-gray-200 focus:border-blue-500 focus:ring-blue-200/50',
                                            {
                                                'border-red-300 focus:border-red-500 focus:ring-red-200/50':
                                                    formErrors.name,
                                            },
                                        ]"
                                    />
                                    <svg
                                        class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"
                                        />
                                        <circle cx="12" cy="7" r="4" />
                                    </svg>
                                </div>
                                <p
                                    v-if="formErrors.name"
                                    class="text-sm text-red-500"
                                >
                                    {{ formErrors.name }}
                                </p>
                            </div>

                            <!-- Email поле аналогично имени -->
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold"
                                    >Email</label
                                >
                                <div class="relative">
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        class="w-full h-12 px-4 pl-10 bg-white/50 border rounded-xl transition-all duration-300 focus:ring-4"
                                        :class="[
                                            'border-gray-200 focus:border-blue-500 focus:ring-blue-200/50',
                                            {
                                                'border-red-300 focus:border-red-500 focus:ring-red-200/50':
                                                    formErrors.email,
                                            },
                                        ]"
                                    />
                                    <svg
                                        class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"
                                        />
                                        <path d="M22 6l-10 7L2 6" />
                                    </svg>
                                </div>
                                <p
                                    v-if="formErrors.email"
                                    class="text-sm text-red-500"
                                >
                                    {{ formErrors.email }}
                                </p>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold"
                                    >Сообщение</label
                                >
                                <div class="relative">
                                    <textarea
                                        v-model="form.message"
                                        class="w-full h-32 px-4 pl-10 py-3 bg-white/50 border rounded-xl resize-none transition-all duration-300 focus:ring-4"
                                        :class="[
                                            'border-gray-200 focus:border-blue-500 focus:ring-blue-200/50',
                                            {
                                                'border-red-300 focus:border-red-500 focus:ring-red-200/50':
                                                    formErrors.message,
                                            },
                                        ]"
                                    ></textarea>
                                    <svg
                                        class="w-5 h-5 text-gray-400 absolute left-3 top-4"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                    >
                                        <path
                                            d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
                                        />
                                    </svg>
                                </div>
                                <p
                                    v-if="formErrors.message"
                                    class="text-sm text-red-500"
                                >
                                    {{ formErrors.message }}
                                </p>
                            </div>

                            <button
                                type="submit"
                                class="w-full relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 text-white py-3 rounded-xl font-semibold disabled:opacity-70 group"
                                :disabled="isSubmitting"
                            >
                                <span
                                    class="relative z-10 flex items-center justify-center"
                                >
                                    <svg
                                        v-if="isSubmitting"
                                        class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
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
                                        />
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        />
                                    </svg>
                                    {{
                                        isSubmitting
                                            ? "Отправка..."
                                            : "Отправить"
                                    }}
                                </span>
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-indigo-600 via-violet-600 to-blue-600 opacity-0 group-hover:opacity-100 transition-opacity"
                                ></div>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer с улучшенными эффектами -->
        <footer class="bg-gray-900 text-gray-400 py-12">
            <div class="container mx-auto px-6">
                <div class="grid md:grid-cols-4 gap-8">
                    <div class="space-y-6">
                        <div
                            class="group flex items-center space-x-2 text-white"
                        >
                            <svg
                                class="w-8 h-8 transform transition-transform group-hover:rotate-12"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                />
                            </svg>
                            <span class="text-xl font-bold">PaySystem</span>
                        </div>
                        <p class="text-sm">
                            Современная платежная система для вашего бизнеса
                        </p>
                        <div class="flex space-x-4">
                            <template
                                v-for="(social, index) in socialLinks"
                                :key="index"
                            >
                                <a
                                    :href="social.href"
                                    class="w-10 h-10 rounded-full flex items-center justify-center border border-gray-700 hover:border-gray-500 hover:text-white transition-all duration-300 group"
                                >
                                    <svg
                                        class="w-5 h-5 transform transition-transform group-hover:scale-110"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            :d="social.icon"
                                        />
                                    </svg>
                                </a>
                            </template>
                        </div>
                    </div>

                    <template
                        v-for="(column, index) in footerColumns"
                        :key="index"
                    >
                        <div>
                            <h4 class="text-white font-semibold mb-4">
                                {{ column.title }}
                            </h4>
                            <ul class="space-y-2 text-sm">
                                <li
                                    v-for="(link, linkIndex) in column.links"
                                    :key="linkIndex"
                                >
                                    <a
                                        :href="link.href"
                                        class="hover:text-white transition-colors flex items-center group"
                                    >
                                        <span class="relative">
                                            {{ link.text }}
                                            <span
                                                class="absolute -bottom-px left-0 w-0 h-px bg-white transition-all group-hover:w-full"
                                            ></span>
                                        </span>
                                        <svg
                                            v-if="link.external"
                                            class="w-4 h-4 ml-1 opacity-50 group-hover:opacity-100 transition-opacity"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                            />
                                        </svg>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </template>
                </div>

                <div
                    class="border-t border-gray-800 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center"
                >
                    <div class="flex items-center space-x-2">
                        <div
                            class="w-2 h-2 rounded-full bg-green-500 animate-pulse"
                        ></div>
                        <p class="text-sm">Все системы работают нормально</p>
                    </div>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <template
                            v-for="(link, index) in legalLinks"
                            :key="index"
                        >
                            <a
                                :href="link.href"
                                class="text-sm hover:text-white transition-colors relative group"
                            >
                                {{ link.text }}
                                <span
                                    class="absolute -bottom-px left-0 w-0 h-px bg-white transition-all group-hover:w-full"
                                ></span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
/* Базовые анимации */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Анимация для градиентов */
.gradient-animate {
    background-size: 200% 200%;
    animation: gradient 15s ease infinite;
}

@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

/* Кастомные стили для скроллбара */
::-webkit-scrollbar {
    width: 10px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 5px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
    border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #2563eb, #7c3aed);
}

/* Утилиты для анимаций */
.scale-102 {
    --tw-scale-x: 1.02;
    --tw-scale-y: 1.02;
    transform: translate(var(--tw-translate-x), var(--tw-translate-y))
        rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y))
        scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

/* Анимации для всплытия элементов */
@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-up {
    animation: slideUp 0.6s ease-out forwards;
}

/* Поддержка градиентного текста в Safari */
@supports (-webkit-background-clip: text) {
    .bg-gradient-text {
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
}

/* Улучшенные фокус-стили */
.focus-ring {
    @apply focus:outline-none focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500;
}
</style>

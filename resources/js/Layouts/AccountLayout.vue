<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link } from "@inertiajs/vue3";
import {
    LayoutDashboard,
    History,
    Menu,
    LogOut,
    ChevronRight,
    Bell,
    Search,
    Settings,
    CreditCard,
    Users,
    Building,
    KeyRound,
} from "lucide-vue-next";

const sidebarOpen = ref(false);
const searchQuery = ref("");
const isCurrentRoute = (name) => route().current(name);

// Навигационные элементы остаются теми же
const navigationItems = [
    {
        name: "Главная",
        route: "dashboard",
        icon: LayoutDashboard,
    },
    {
        name: "Транзакции",
        route: "transaction",
        icon: History,
    },
    {
        name: "Мерчант",
        route: "merchant",
        icon: Building,
    },
    {
        name: "Пополнение",
        route: "deposit",
        icon: Building,
    },
    {
        name: "Перевод",
        route: "transfer",
        icon: Building,
    },
    {
        name: "Вывод средств",
        route: "withdrawal",
        icon: CreditCard,
        badge: 3,
    },
    {
        name: "Партнерам",
        route: "partners",
        icon: Users,
    },
];

// Остальная логика остается той же
const profileDropdownOpen = ref(false);
const notificationsDropdownOpen = ref(false);

const notifications = ref([
    {
        id: 1,
        title: "Новое обновление системы",
        time: "5 мин назад",
        read: false,
    },
    { id: 2, title: "Входящий платеж", time: "1 час назад", read: false },
]);

const unreadNotifications = computed(() => {
    return notifications.value.filter((n) => !n.read).length;
});

// Handlers остаются теми же
const toggleProfileDropdown = (event) => {
    event.stopPropagation();
    profileDropdownOpen.value = !profileDropdownOpen.value;
    if (profileDropdownOpen.value) {
        notificationsDropdownOpen.value = false;
    }
};

const toggleNotificationsDropdown = (event) => {
    event.stopPropagation();
    notificationsDropdownOpen.value = !notificationsDropdownOpen.value;
    if (notificationsDropdownOpen.value) {
        profileDropdownOpen.value = false;
    }
};

const closeDropdowns = (event) => {
    const isProfileClick = event.target.closest(".profile-dropdown");
    const isNotificationsClick = event.target.closest(
        ".notifications-dropdown"
    );

    if (!isProfileClick) {
        profileDropdownOpen.value = false;
    }
    if (!isNotificationsClick) {
        notificationsDropdownOpen.value = false;
    }
};

const markAllNotificationsAsRead = () => {
    notifications.value = notifications.value.map((n) => ({
        ...n,
        read: true,
    }));
};

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const closeSidebar = () => {
    sidebarOpen.value = false;
};

onMounted(() => {
    document.addEventListener("click", closeDropdowns);
});

onUnmounted(() => {
    document.removeEventListener("click", closeDropdowns);
});
</script>

<template>
    <div class="flex h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <!-- Overlay с размытием -->
        <div
            v-show="sidebarOpen"
            @click="closeSidebar"
            class="fixed inset-0 z-20 bg-gray-900/30 backdrop-blur-sm lg:hidden"
        ></div>

        <!-- Обновленный сайдбар -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-30 w-72 transform transition-all duration-300 ease-in-out bg-white/80 backdrop-blur-xl border-r border-gray-100',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'lg:translate-x-0 lg:static',
            ]"
        >
            <!-- Обновленный логотип -->
            <div class="flex items-center gap-3 px-6 h-20">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-violet-500 to-violet-700 rounded-xl flex items-center justify-center shadow-lg shadow-violet-500/20"
                >
                    <span class="text-white font-bold text-lg">PS</span>
                </div>
                <div class="flex flex-col">
                    <div
                        class="font-semibold text-gray-900 text-lg tracking-tight"
                    >
                        Payment System
                    </div>
                </div>
            </div>

            <!-- Обновленная навигация -->
            <nav class="mt-6 px-4">
                <Link
                    v-for="item in navigationItems"
                    :key="item.name"
                    :href="route(item.route)"
                    :class="[
                        'flex items-center h-11 gap-3 px-4 rounded-xl text-sm mb-1.5 transition-all duration-200',
                        isCurrentRoute(item.route)
                            ? 'bg-violet-500 text-white shadow-lg shadow-violet-500/20'
                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                    ]"
                >
                    <component
                        :is="item.icon"
                        :class="[
                            'w-5 h-5 transition-colors',
                            isCurrentRoute(item.route)
                                ? 'text-white'
                                : 'text-gray-400 group-hover:text-gray-600',
                        ]"
                    />
                    <span class="font-medium">{{ item.name }}</span>

                    <div
                        v-if="item.badge"
                        class="ml-auto px-2 py-0.5 text-xs font-semibold rounded-full"
                        :class="[
                            isCurrentRoute(item.route)
                                ? 'bg-white/20 text-white'
                                : 'bg-violet-100 text-violet-700',
                        ]"
                    >
                        {{ item.badge }}
                    </div>
                </Link>
            </nav>

            <!-- Обновленный разделитель -->
            <div class="mx-4 my-4 border-t border-gray-100/50"></div>

            <!-- Обновленные дополнительные пункты -->
            <div class="px-4">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center h-11 gap-3 px-4 rounded-xl text-sm text-gray-600 hover:bg-red-50 hover:text-red-600 transition-colors w-full group"
                >
                    <LogOut
                        class="w-5 h-5 text-gray-400 group-hover:text-red-500"
                    />
                    <span class="font-medium">Выход</span>
                </Link>
            </div>
        </aside>

        <!-- Основной контент -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Обновленная шапка -->
            <header
                class="h-20 bg-white/80 backdrop-blur-xl border-b border-gray-100"
            >
                <div class="h-full px-8 flex items-center justify-between">
                    <!-- Левая часть -->
                    <div class="flex items-center gap-8">
                        <button
                            @click="toggleSidebar"
                            class="lg:hidden text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <Menu class="w-6 h-6" />
                        </button>
                    </div>

                    <!-- Правая часть -->
                    <div class="flex items-center gap-6">
                        <!-- Обновленный разделитель -->
                        <div class="h-8 w-px bg-gray-200/70"></div>

                        <!-- Обновленный профиль -->
                        <div class="relative profile-dropdown">
                            <button
                                @click="toggleProfileDropdown"
                                class="flex items-center gap-3 group"
                            >
                                <div
                                    class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-100 to-violet-200 flex items-center justify-center transition-transform group-hover:scale-105"
                                >
                                    <span
                                        class="text-sm font-semibold text-violet-700"
                                    >
                                        {{
                                            $page.props.user.email
                                                .charAt(0)
                                                .toUpperCase()
                                        }}
                                    </span>
                                </div>
                            </button>

                            <!-- Обновленное выпадающее меню профиля -->
                            <div
                                v-if="profileDropdownOpen"
                                class="absolute right-0 mt-3 w-80 bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-gray-100/50 overflow-hidden"
                            >
                                <!-- Шапка профиля -->
                                <div
                                    class="p-5 bg-gradient-to-br from-violet-500/5 to-violet-600/5 border-b border-gray-100/50"
                                >
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-violet-100 to-violet-200 flex items-center justify-center"
                                        >
                                            <span
                                                class="text-base font-semibold text-violet-700"
                                            >
                                                {{
                                                    $page.props.user.email
                                                        .charAt(0)
                                                        .toUpperCase()
                                                }}
                                            </span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p
                                                class="text-base font-medium text-gray-900 truncate"
                                            >
                                                {{ $page.props.user.email }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Обновленная информация и статистика -->
                                <div
                                    class="px-5 py-4 border-b border-gray-100/50"
                                >
                                    <div class="grid grid-cols-2 gap-6">
                                        <div>
                                            <p
                                                class="text-xs font-medium text-gray-500"
                                            >
                                                ID пользователя
                                            </p>
                                            <p
                                                class="text-sm font-semibold text-violet-600 mt-1"
                                            >
                                                #{{ $page.props.user.identify }}
                                            </p>
                                        </div>
                                        <div>
                                            <p
                                                class="text-xs font-medium text-gray-500"
                                            >
                                                Последний вход
                                            </p>
                                            <p
                                                class="text-sm font-medium text-gray-900 mt-1"
                                            >
                                                Сегодня, 14:23
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Обновленные действия -->
                                <div class="p-3">
                                    <Link
                                        v-for="(action, index) in [
                                            {
                                                icon: Settings,
                                                text: 'Настройки профиля',
                                                route: 'dashboard',
                                            },
                                            {
                                                icon: KeyRound,
                                                text: 'Безопасность',
                                                route: 'dashboard',
                                            },
                                        ]"
                                        :key="index"
                                        :href="route(action.route)"
                                        class="w-full flex items-center px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 rounded-xl transition-colors group"
                                    >
                                        <component
                                            :is="action.icon"
                                            class="w-4 h-4 mr-3 text-gray-400 group-hover:text-violet-600"
                                        />
                                        <span
                                            class="group-hover:text-gray-900"
                                            >{{ action.text }}</span
                                        >
                                    </Link>

                                    <div
                                        class="h-px bg-gray-100 my-2 mx-3"
                                    ></div>

                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="w-full flex items-center px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-xl transition-colors group"
                                    >
                                        <LogOut
                                            class="w-4 h-4 mr-3 text-red-500"
                                        />
                                        <span>Выйти из системы</span>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Основной контент -->
            <main
                class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-gray-50 to-gray-100 p-8"
            >
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style>
:root {
    --violet-50: #f5f3ff;
    --violet-100: #ede9fe;
    --violet-500: #8b5cf6;
    --violet-600: #7c3aed;
    --violet-700: #6d28d9;
}

/* Обновленные стили для скроллбара */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}

/* Дополнительные стили для анимаций */
.transition-transform {
    transition-property: transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Улучшенные эффекты размытия */
.backdrop-blur-xl {
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
}
</style>



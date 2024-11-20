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
        route: "transactions",
        icon: History,
    },
    {
        name: "Мерчант",
        route: "merchant",
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
    <div class="flex h-screen bg-[#F7F7F9]">
        <!-- Overlay -->
        <div
            v-show="sidebarOpen"
            @click="closeSidebar"
            class="fixed inset-0 z-20 bg-gray-900/20 backdrop-blur-sm lg:hidden"
        ></div>

        <!-- Сайдбар -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-30 w-64 transform transition-transform duration-300 ease-in-out bg-white',
                sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'lg:translate-x-0 lg:static',
            ]"
        >
            <!-- Логотип -->
            <div class="flex items-center gap-3 px-5 h-16">
                <div
                    class="w-9 h-9 bg-violet-600 rounded-lg flex items-center justify-center"
                >
                    <span class="text-white font-bold">PS</span>
                </div>
                <div class="flex flex-col">
                    <div class="font-semibold text-gray-900">Payment System</div>
                    <!-- <div class="text-xs text-gray-500">Digital Market V1</div> -->
                </div>
            </div>

            <!-- Навигация -->
            <nav class="mt-5 px-4">
                <Link
                    v-for="item in navigationItems"
                    :key="item.name"
                    :href="route(item.route)"
                    :class="[
                        'flex items-center h-10 gap-3 px-3 rounded-lg text-sm mb-1 transition-colors',
                        isCurrentRoute(item.route)
                            ? 'bg-violet-50 text-violet-700'
                            : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900',
                    ]"
                >
                    <component
                        :is="item.icon"
                        :class="[
                            'w-5 h-5',
                            isCurrentRoute(item.route)
                                ? 'text-violet-600'
                                : 'text-gray-400',
                        ]"
                    />
                    <span class="font-medium">{{ item.name }}</span>

                    <div
                        v-if="item.badge"
                        class="ml-auto px-2 py-0.5 text-xs font-medium rounded-full"
                        :class="[
                            isCurrentRoute(item.route)
                                ? 'bg-violet-100 text-violet-700'
                                : 'bg-gray-100 text-gray-600',
                        ]"
                    >
                        {{ item.badge }}
                    </div>
                </Link>
            </nav>

            <!-- Разделитель -->
            <div class="mx-4 my-4 border-t border-gray-100"></div>

            <!-- Дополнительные пункты -->
            <div class="px-4">
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="flex items-center h-10 gap-3 px-3 rounded-lg text-sm text-gray-500 hover:bg-gray-50 hover:text-gray-900 w-full"
                >
                    <LogOut class="w-5 h-5 text-gray-400" />
                    <span class="font-medium">Выход</span>
                </Link>
            </div>
        </aside>

        <!-- Основной контент -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Шапка -->
            <header class="h-16 bg-white shadow-sm">
                <div class="h-full px-6 flex items-center justify-between">
                    <!-- Левая часть -->
                    <div class="flex items-center gap-8">
                        <button
                            @click="toggleSidebar"
                            class="lg:hidden text-gray-400 hover:text-gray-600"
                        >
                            <Menu class="w-6 h-6" />
                        </button>
                    </div>

                    <!-- Правая часть -->
                    <div class="flex items-center gap-6">
                        <!-- Разделитель -->
                        <div class="h-6 w-px bg-gray-200"></div>

                        <!-- Профиль -->
                        <div class="relative profile-dropdown">
                            <button
                                @click="toggleProfileDropdown"
                                class="flex items-center gap-3"
                            >
                                <div
                                    class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center"
                                >
                                    <span
                                        class="text-sm font-medium text-violet-700"
                                    >
                                        {{
                                            $page.props.user.email
                                                .charAt(0)
                                                .toUpperCase()
                                        }}
                                    </span>
                                </div>
                            </button>

                            <div
                                v-if="profileDropdownOpen"
                                class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden"
                            >
                                <!-- Шапка профиля -->
                                <div
                                    class="p-4 bg-gray-50/50 border-b border-gray-100"
                                >
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center"
                                        >
                                            <span
                                                class="text-sm font-medium text-violet-700"
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
                                                class="text-sm font-medium text-gray-900 truncate"
                                            >
                                                {{ $page.props.user.email }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Информация и статистика -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p
                                                class="text-xs font-medium text-gray-500"
                                            >
                                                ID пользователя
                                            </p>
                                            <p
                                                class="text-sm font-medium text-violet-600 mt-0.5"
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
                                                class="text-sm text-gray-900 mt-0.5"
                                            >
                                                Сегодня, 14:23
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Действия -->
                                <div class="p-2">
                                    <!-- Настройки профиля -->
                                    <Link
                                        :href="route('dashboard')"
                                        class="w-full flex items-center px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition-colors group"
                                    >
                                        <Settings
                                            class="w-4 h-4 mr-2 text-gray-400 group-hover:text-gray-600"
                                        />
                                        <span>Настройки профиля</span>
                                    </Link>

                                    <!-- Безопасность -->
                                    <Link
                                        :href="route('dashboard')"
                                        class="w-full flex items-center px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition-colors group"
                                    >
                                        <KeyRound
                                            class="w-4 h-4 mr-2 text-gray-400 group-hover:text-gray-600"
                                        />
                                        <span>Безопасность</span>
                                    </Link>

                                    <!-- Разделитель -->
                                    <div
                                        class="h-px bg-gray-100 my-2 mx-2"
                                    ></div>

                                    <!-- Выход -->
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="w-full flex items-center px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors group"
                                    >
                                        <LogOut
                                            class="w-4 h-4 mr-2 text-red-500"
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
                class="flex-1 overflow-x-hidden overflow-y-auto bg-[#F7F7F9] p-6"
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

/* Стили для скроллбара */
::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #d1d5db;
}
</style>

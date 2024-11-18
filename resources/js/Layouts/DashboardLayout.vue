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
} from "lucide-vue-next";

// Состояния для сайдбара и поиска
const sidebarOpen = ref(false);
const searchQuery = ref("");
const isCurrentRoute = (name) => route().current(name);

// Навигационные элементы
const navigationItems = [
    {
        name: "Главная",
        route: "dashboard",
        icon: LayoutDashboard,
        badge: 3,
    },
    {
        name: "Транзакции",
        route: "transactions",
        icon: History,
    },
    {
        name: "Мерчант",
        route: "dashboard",
        icon: Building,
    },
    {
        name: "Платежи",
        route: "dashboard",
        icon: CreditCard,
    },
    {
        name: "Настройки",
        route: "dashboard",
        icon: Settings,
    },
    {
        name: "Партнерам",
        route: "dashboard",
        icon: Users,
    },
];

// Фильтрация навигации
const filteredNavigation = computed(() => {
    if (!searchQuery.value) return navigationItems;
    return navigationItems.filter((item) =>
        item.name.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

// Состояния для дропдаунов
const profileDropdownOpen = ref(false);
const notificationsDropdownOpen = ref(false);

// Уведомления
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

// Обработчики для дропдаунов
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

// Закрытие дропдаунов при клике вне
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

// Обработка уведомлений
const markAllNotificationsAsRead = () => {
    notifications.value = notifications.value.map((n) => ({
        ...n,
        read: true,
    }));
};

// Управление сайдбаром для мобильных устройств
const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};

const closeSidebar = () => {
    sidebarOpen.value = false;
};

// Lifecycle hooks
onMounted(() => {
    document.addEventListener("click", closeDropdowns);
});

onUnmounted(() => {
    document.removeEventListener("click", closeDropdowns);
});
</script>

<template>
    <!-- Оставьте ваш текущий template без изменений, 
         но убедитесь, что используются правильные обработчики событий -->
    <div
        class="flex h-screen bg-gradient-to-br from-slate-50 via-white to-blue-50"
    >
        <!-- Overlay -->
        <div
            v-show="sidebarOpen"
            @click="closeSidebar"
            class="fixed z-20 inset-0 bg-slate-900/20 backdrop-blur-sm transition-opacity lg:hidden"
        ></div>

        <!-- Sidebar -->
        <div
            :class="[
                sidebarOpen
                    ? 'translate-x-0 ease-out'
                    : '-translate-x-full ease-in',
                'fixed z-30 inset-y-0 left-0 w-72 transition-all duration-300 transform bg-white/95 backdrop-blur-xl border-r border-slate-100 overflow-y-auto lg:translate-x-0 lg:static lg:inset-0 shadow-xl shadow-blue-100/50',
            ]"
        >
            <!-- Logo Container -->
            <div
                class="flex items-center justify-center h-16 px-6 border-b border-slate-100"
            >
                <div class="w-full">
                    <a
                        href="/dashboard"
                        class="flex items-center justify-center"
                    >
                        <img
                            class="h-8 hover:opacity-80 transition-opacity"
                            src="https://www.amug.com/wp-content/uploads/2016/09/you-logo-here.png"
                            alt="Logo"
                        />
                    </a>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-3">
                <Link
                    v-for="item in filteredNavigation"
                    :key="item.name"
                    :href="route(item.route)"
                    :class="[
                        isCurrentRoute(item.route)
                            ? 'bg-blue-50 text-blue-600 shadow-sm'
                            : 'text-slate-600 hover:text-blue-600 hover:bg-blue-50/50',
                        'flex items-center px-4 py-3 rounded-xl transition-all duration-200 group relative',
                    ]"
                >
                    <component
                        :is="item.icon"
                        :class="[
                            isCurrentRoute(item.route)
                                ? 'text-blue-600'
                                : 'text-slate-400 group-hover:text-blue-600',
                            'w-5 h-5 mr-3 transition-colors',
                        ]"
                    />
                    <span class="font-medium">{{ item.name }}</span>

                    <!-- Badge -->
                    <div
                        v-if="item.badge"
                        class="absolute right-4 bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full text-xs font-semibold"
                    >
                        {{ item.badge }}
                    </div>

                    <ChevronRight
                        class="ml-auto w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity text-slate-400"
                    />
                </Link>
            </nav>

            <!-- Logout Button -->
            <div
                class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-100 bg-white/80 backdrop-blur-sm"
            >
                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="w-full py-3 px-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white rounded-xl transition-all duration-200 font-medium flex items-center justify-center gap-3 group shadow-lg shadow-blue-200/50 hover:shadow-blue-300/50"
                >
                    <LogOut
                        class="w-4 h-4 transition-transform group-hover:-translate-x-1"
                    />
                    <span class="text-sm">Выход</span>
                </Link>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header
                class="bg-white/90 backdrop-blur-xl border-b border-slate-100 shadow-sm"
            >
                <div class="flex justify-between items-center h-16 px-6">
                    <div class="flex items-center gap-4">
                        <button
                            @click="toggleSidebar"
                            class="text-slate-500 hover:text-slate-700 lg:hidden"
                        >
                            <Menu class="w-6 h-6" />
                        </button>
                    </div>

                    <!-- Right Section with Notifications and Profile -->
                    <div class="flex items-center gap-4">
                        <!-- Notifications -->
                        <div class="relative notifications-dropdown">
                            <button
                                class="relative p-2 text-slate-500 hover:text-slate-700 transition-colors rounded-lg hover:bg-slate-100"
                                @click="toggleNotificationsDropdown"
                            >
                                <Bell class="w-5 h-5" />
                                <span
                                    v-if="unreadNotifications"
                                    class="absolute top-1 right-1 w-2 h-2 bg-blue-500 rounded-full notification-dot"
                                />
                            </button>

                            <!-- Notifications Panel -->
                            <Transition
                                enter-active-class="transition duration-200 ease-out"
                                enter-from-class="transform scale-95 opacity-0"
                                enter-to-class="transform scale-100 opacity-100"
                                leave-active-class="transition duration-75 ease-in"
                                leave-from-class="transform scale-100 opacity-100"
                                leave-to-class="transform scale-95 opacity-0"
                            >
                                <div
                                    v-if="notificationsDropdownOpen"
                                    class="absolute right-0 mt-3 w-80 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden"
                                >
                                    <div
                                        class="p-4 bg-gradient-to-r from-slate-50 to-blue-50 border-b border-slate-100"
                                    >
                                        <div
                                            class="flex justify-between items-center"
                                        >
                                            <h3
                                                class="font-semibold text-slate-800"
                                            >
                                                Уведомления
                                            </h3>
                                            <button
                                                v-if="unreadNotifications"
                                                @click="
                                                    markAllNotificationsAsRead
                                                "
                                                class="text-xs text-blue-600 hover:text-blue-700"
                                            >
                                                Отметить все как прочитанные
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        class="divide-y divide-slate-100 max-h-[300px] overflow-y-auto"
                                    >
                                        <div
                                            v-for="notification in notifications"
                                            :key="notification.id"
                                            class="p-4 hover:bg-slate-50 transition-colors cursor-pointer"
                                            :class="{
                                                'bg-blue-50/50':
                                                    !notification.read,
                                            }"
                                        >
                                            <div
                                                class="flex justify-between items-start"
                                            >
                                                <p
                                                    class="text-sm font-medium text-slate-800"
                                                >
                                                    {{ notification.title }}
                                                </p>
                                                <span
                                                    class="text-xs text-slate-500"
                                                    >{{
                                                        notification.time
                                                    }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        v-if="!notifications.length"
                                        class="p-4 text-center text-sm text-slate-500"
                                    >
                                        Нет новых уведомлений
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- Profile -->
                        <div class="relative profile-dropdown">
                            <button
                                @click="toggleProfileDropdown"
                                class="flex items-center gap-3 focus:outline-none"
                            >
                                <span class="hidden md:block text-right">
                                    <p
                                        class="text-sm font-medium text-slate-700"
                                    >
                                        {{ $page.props.user.email }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        Администратор
                                    </p>
                                </span>
                                <div
                                    class="relative h-10 w-10 rounded-full overflow-hidden ring-2 ring-offset-2 ring-blue-100 hover:ring-blue-200 transition-all"
                                >
                                    <img
                                        class="h-full w-full object-cover"
                                        src="https://images.unsplash.com/photo-1528892952291-009c663ce843?ixlib=rb-1.2.1&amp;auto=format&amp;fit=crop&amp;w=296&amp;q=80"
                                        alt="Your avatar"
                                    />
                                </div>
                            </button>

                            <!-- Profile Dropdown -->
                            <Transition
                                enter-active-class="transition duration-200 ease-out"
                                enter-from-class="transform scale-95 opacity-0"
                                enter-to-class="transform scale-100 opacity-100"
                                leave-active-class="transition duration-75 ease-in"
                                leave-from-class="transform scale-100 opacity-100"
                                leave-to-class="transform scale-95 opacity-0"
                            >
                                <div
                                    v-if="profileDropdownOpen"
                                    class="absolute right-0 mt-3 w-72 bg-white rounded-xl shadow-xl border border-slate-100 overflow-hidden"
                                >
                                    <div
                                        class="p-4 bg-gradient-to-r from-slate-50 to-blue-50 border-b border-slate-100"
                                    >
                                        <p class="text-xs text-slate-500">
                                            Вы вошли как
                                        </p>
                                        <p
                                            class="text-sm font-medium text-slate-800 truncate"
                                        >
                                            {{ $page.props.user.email }}
                                        </p>
                                    </div>
                                    <div class="p-2">
                                        <Link
                                            :href="route('logout')"
                                            method="post"
                                            class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg gap-3 transition-colors"
                                        >
                                            <LogOut class="w-4 h-4" />
                                            <span>Выйти из системы</span>
                                        </Link>
                                    </div>
                                </div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main
                class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-slate-50 via-white to-blue-50 p-6"
            >
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Скроллбар */
</style>

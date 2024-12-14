<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import {
    LayoutDashboard,
    ArrowRightLeft,
    Building,
    Wallet,
    CircleDollarSign,
    Users,
    Menu,
    Store,
    Settings,
    CreditCard,
    FileText,
    PieChart,
} from "lucide-vue-next";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const navigationItems = [
    {
        name: "Главная",
        route: "dashboard",
        icon: LayoutDashboard,
    },
    {
        name: "Транзакции",
        route: "transaction",
        icon: ArrowRightLeft,
    },
    {
        name: "Мерчант",
        route: "merchant",
        icon: Store,
    },
    {
        name: "Пополнение",
        route: "deposit",
        icon: Wallet,
    },
    {
        name: "Перевод",
        route: "transfer",
        icon: ArrowRightLeft,
    },
    {
        name: "Вывод средств",
        route: "withdrawal",
        icon: CircleDollarSign,
        badge: 3,
    },
    {
        name: "Партнерам",
        route: "partners",
        icon: Users,
    },
];

const isCurrentRoute = (name) => {
    try {
        return route().current(name);
    } catch (e) {
        console.error(`Route '${name}' is not defined.`);
        return false;
    }
};
</script>

<template>
    <aside
        class="w-72 min-h-screen bg-gradient-to-b from-violet-100 to-white shadow-2xl border-r border-gray-200"
    >
        <!-- Logo -->
        <div class="flex items-center gap-2 px-6 h-20 border-b border-gray-200">
            <div
                class="w-10 h-10 rounded-2xl bg-violet-500 flex items-center justify-center shadow-lg"
            >
                <Wallet class="w-5 h-5 text-white" />
            </div>
            <span class="text-gray-900 text-lg font-bold">Finsystem</span>
            <span class="text-violet-500">DMG</span>
        </div>

        <!-- User Profile -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 rounded-full overflow-hidden border border-gray-300 shadow-sm transform transition-transform duration-300 hover:scale-105"
                >
                    <img
                        :src="`https://ui-avatars.com/api/?name=${user.email}&background=random`"
                        alt="profile"
                        class="w-full h-full object-cover"
                    />
                </div>
                <div>
                    <p class="text-gray-900 font-medium">
                        Hi, {{ user.email.split("@")[0] }}
                    </p>
                    <p class="text-gray-500 text-sm">{{ user.email }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="mt-4 px-4">
            <ul class="space-y-2">
                <li v-for="item in navigationItems" :key="item.name">
                    <Link
                        :href="route(item.route)"
                        class="flex items-center px-4 py-3 rounded-xl transition-all duration-300 hover:bg-violet-500 hover:shadow-md hover:scale-105 group"
                        :class="[
                            isCurrentRoute(item.route)
                                ? 'bg-violet-500 text-white shadow-md scale-105'
                                : 'text-gray-600 hover:text-white',
                        ]"
                    >
                        <component
                            :is="item.icon"
                            class="w-5 h-5 mr-3 transform transition-transform duration-300 group-hover:translate-x-1"
                            :class="[
                                isCurrentRoute(item.route)
                                    ? 'text-white'
                                    : 'text-gray-400 group-hover:text-white',
                            ]"
                        />
                        <span class="font-medium">{{ item.name }}</span>
                        <div
                            v-if="item.badge"
                            class="ml-auto px-2 py-0.5 text-xs font-medium rounded-lg bg-white text-violet-500"
                        >
                            {{ item.badge }}
                        </div>
                    </Link>
                </li>
            </ul>
        </nav>

        <!-- Footer -->
        <div class="absolute bottom-6 left-0 w-full px-6">
            <div class="text-sm text-gray-400">
                <p>Dompet Payment Admin Dashboard</p>
                <p class="mt-1">&copy; 2023 All Rights Reserved</p>
                <div class="flex items-center gap-1 mt-2">
                    <span>Made with</span>
                    <svg
                        class="w-4 h-4 text-gray-400 animate-pulse"
                        fill="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"
                        />
                    </svg>
                    <span>by DexignLab</span>
                </div>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.transition-all {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.animate-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%,
    100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.1);
    }
}

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
</style>

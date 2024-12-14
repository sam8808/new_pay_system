<script setup>
import Sidebar from "@/Components/Sidebar.vue";
import Header from "@/Components/Header.vue";
import { useNotifications } from "@/Composables/useNotifications";
import {
    Bell,
    Search,
    Moon,
    ShoppingCart,
    MessageSquare,
    FileText,
} from "lucide-vue-next";

const {
    searchQuery,
    unreadNotifications,
    profileDropdownOpen,
    notificationsDropdownOpen,
    toggleProfileDropdown,
    toggleNotificationsDropdown,
    markAllNotificationsAsRead,
} = useNotifications();
</script>

<template>
    <div class="flex h-screen bg-gray-100">
        <!-- Сайдбар -->
        <Sidebar :user="$page.props.user" />

        <!-- Основной контент -->
        <div class="flex-1">
            <!-- Хедер -->
            <Header />

            <!-- Основной контент -->
            <main class="p-8">
                <div class="max-w-7xl mx-auto">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<style>
/* Скроллбар */
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

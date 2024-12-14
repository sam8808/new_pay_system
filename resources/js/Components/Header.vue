<script setup>
import { ref } from "vue";
import { Bell, Wallet } from "lucide-vue-next";

const unreadNotifications = ref(3);
const balance = ref(824571.93); // Пример баланса

const toggleNotificationsDropdown = () => {
    // Логика для отображения/скрытия дропдауна уведомлений
};
</script>

<template>
    <header class="h-16 bg-gradient-to-b from-violet-100 to-white border-b border-gray-200 shadow-lg">
        <div class="h-full px-8 flex items-center justify-between">
            <!-- Баланс -->
            <div class="flex items-center gap-3">
                <Wallet class="w-6 h-6 text-violet-600" />
                <span class="text-xl font-semibold text-gray-800">{{ balance }} USD</span>
            </div>

            <!-- Уведомления -->
            <button
                @click="toggleNotificationsDropdown"
                class="w-9 h-9 flex items-center justify-center rounded-full hover:bg-gray-100 relative notifications-dropdown transition-transform duration-300 hover:scale-110"
            >
                <Bell class="w-5 h-5 text-gray-500" />
                <span
                    v-if="unreadNotifications"
                    class="absolute -top-1 -right-1 w-5 h-5 bg-violet-500 text-white text-xs rounded-full flex items-center justify-center"
                >
                    {{ unreadNotifications }}
                </span>
            </button>
        </div>
    </header>
</template>

<style scoped>
.transition-transform {
    transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
        0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>

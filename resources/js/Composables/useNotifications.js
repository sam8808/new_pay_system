import { ref, computed, onMounted, onUnmounted } from "vue";

export function useNotifications() {
    // Состояния
    const notifications = ref([
        {
            id: 1,
            title: "Новое обновление системы",
            time: "5 мин назад",
            read: false,
        },
        {
            id: 2,
            title: "Входящий платеж",
            time: "1 час назад",
            read: false,
        },
    ]);

    const sidebarOpen = ref(false);
    const profileDropdownOpen = ref(false);
    const notificationsDropdownOpen = ref(false);
    const searchQuery = ref("");

    // Вычисляемые свойства
    const unreadNotifications = computed(() => {
        return notifications.value.filter((n) => !n.read).length;
    });

    // Методы
    const toggleSidebar = () => {
        sidebarOpen.value = !sidebarOpen.value;
    };

    const closeSidebar = () => {
        sidebarOpen.value = false;
    };

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

    // Жизненный цикл
    onMounted(() => {
        document.addEventListener("click", closeDropdowns);
    });

    onUnmounted(() => {
        document.removeEventListener("click", closeDropdowns);
    });

    return {
        // Состояния
        notifications,
        sidebarOpen,
        profileDropdownOpen,
        notificationsDropdownOpen,
        searchQuery,
        // Вычисляемые свойства
        unreadNotifications,
        // Методы
        toggleSidebar,
        closeSidebar,
        toggleProfileDropdown,
        toggleNotificationsDropdown,
        markAllNotificationsAsRead,
    };
}

<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
  Home, 
  Users, 
  Store, 
  CreditCard, 
  Wallet, 
  History, 
  LogOut,
  Menu,
  Coins,
  Building2,
  ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  newMerchants: {
    type: Number,
    default: 0
  },
  newWithdrawal: {
    type: Number,
    default: 0
  },
  newPayment: {
    type: Number,
    default: 0
  }
});

const sidebarOpen = ref(false);
const dropdownOpen = ref(false);

const navigation = [
  { name: 'Главная', route: 'admin', icon: Home },
  { name: 'Пользователи', route: 'admin.users', icon: Users },
  { 
    name: 'Магазины', 
    route: 'admin.merchant', 
    icon: Store,
    badge: computed(() => props.newMerchants)
  },
  { name: 'Платежные системы', route: 'admin.ps', icon: Building2 },
  { name: 'Валюты', route: 'admin.currency', icon: Coins },
  { 
    name: 'Выплаты', 
    route: 'admin.withdrawal', 
    icon: Wallet,
    badge: computed(() => props.newWithdrawal)
  },
  { 
    name: 'Платежи', 
    route: 'admin.payments', 
    icon: CreditCard,
    badge: computed(() => props.newPayment)
  },
  { name: 'История', route: 'admin.history', icon: History },
];

const currentRoute = computed(() => route().current());

const isActiveRoute = (routeName) => {
  if (routeName === 'admin.merchant') {
    return currentRoute.value === routeName || currentRoute.value === 'admin.merchant.show';
  }
  if (routeName === 'admin.ps') {
    return currentRoute.value === routeName || currentRoute.value === 'admin.ps.create';
  }
  return currentRoute.value === routeName;
};
</script>

<template>
  <div>
    <Head :title="title" />
    
    <div class="flex h-screen bg-gradient-to-br from-gray-50/40 to-gray-100/40">
      <!-- Enhanced Animated Background -->
      <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-1/4 -left-20 w-[500px] h-[500px] bg-emerald-400/20 rounded-full blur-[100px] animate-pulse-slow"></div>
        <div class="absolute bottom-1/4 -right-20 w-[600px] h-[600px] bg-blue-400/20 rounded-full blur-[100px] animate-pulse-slow" style="animation-delay: -5s;"></div>
        <div class="absolute top-1/3 right-1/4 w-[400px] h-[400px] bg-purple-400/20 rounded-full blur-[100px] animate-pulse-slow" style="animation-delay: -10s;"></div>
      </div>

      <!-- Enhanced Sidebar -->
      <div
        :class="[
          'fixed inset-y-0 left-0 z-30 w-64 transform overflow-y-auto bg-white/80 backdrop-blur-2xl border-r border-white/20 transition-all duration-300 lg:static lg:translate-x-0',
          sidebarOpen ? 'translate-x-0' : '-translate-x-full'
        ]"
      >
        <!-- Enhanced Logo Area -->
        <div class="flex items-center justify-center py-8 px-6">
          <img
            class="w-40 transition-all duration-300 hover:scale-105 drop-shadow-xl"
            src="https://www.amug.com/wp-content/uploads/2016/09/you-logo-here.png"
            alt="Logo"
          />
        </div>

        <!-- Enhanced Navigation -->
        <nav class="mt-6 px-4">
          <div class="space-y-1.5">
            <Link
              v-for="item in navigation"
              :key="item.route"
              :href="route(item.route)"
              :class="[
                'group flex items-center rounded-xl px-4 py-3 text-sm font-medium transition-all duration-300',
                isActiveRoute(item.route)
                  ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40'
                  : 'text-gray-700 hover:bg-emerald-50 hover:text-emerald-600'
              ]"
            >
              <component 
                :is="item.icon" 
                :class="[
                  'h-5 w-5 transition-transform duration-300 group-hover:scale-110',
                  isActiveRoute(item.route) ? '' : 'group-hover:text-emerald-500'
                ]" 
              />
              <span class="ml-3">{{ item.name }}</span>
              <span
                v-if="item.badge && item.badge > 0"
                class="ml-auto flex h-5 min-w-[20px] items-center justify-center rounded-full bg-red-500 px-1.5 text-xs font-bold text-white"
              >
                {{ item.badge }}
              </span>
              <ChevronRight 
                :class="[
                  'ml-auto h-4 w-4 transition-transform duration-300',
                  isActiveRoute(item.route) ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'
                ]"
              />
            </Link>
          </div>
        </nav>
      </div>

      <!-- Enhanced Main Content Area -->
      <div class="flex flex-1 flex-col overflow-hidden">
        <!-- Enhanced Header -->
        <header class="flex h-16 items-center justify-between bg-white/80 backdrop-blur-xl border-b border-white/20 px-6">
          <button
            @click="sidebarOpen = true"
            class="rounded-xl p-2 text-gray-700 hover:bg-emerald-50 hover:text-emerald-500 transition-all duration-300 lg:hidden"
          >
            <Menu class="h-6 w-6" />
          </button>

          <div class="relative ml-auto">
            <button
              @click="dropdownOpen = !dropdownOpen"
              class="group flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl ring-2 ring-emerald-500/30 transition-all duration-300 hover:ring-4"
            >
              <img
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                src="https://images.unsplash.com/photo-1528892952291-009c663ce843?ixlib=rb-1.2.1&amp;auto=format&amp;fit=crop&amp;w=296&amp;q=80"
                alt="Profile"
              />
            </button>

            <!-- Enhanced Dropdown -->
            <div
              v-show="dropdownOpen"
              class="absolute right-0 mt-2 w-48 origin-top-right rounded-xl bg-white/90 backdrop-blur-xl py-1 shadow-lg ring-1 ring-black/5 transition-all duration-300"
              :class="{ 'scale-100 opacity-100': dropdownOpen, 'scale-95 opacity-0': !dropdownOpen }"
            >
              <Link
                :href="route('admin.logout')"
                class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 transition-colors duration-200"
              >
                <LogOut class="mr-2 h-4 w-4" />
                Выйти
              </Link>
            </div>
          </div>
        </header>

        <!-- Enhanced Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto p-6 relative">
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>

<style scoped>
@keyframes pulse-slow {
  0%, 100% { 
    opacity: 0.4;
    transform: scale(1);
  }
  50% { 
    opacity: 0.2;
    transform: scale(1.05);
  }
}

.animate-pulse-slow {
  animation: pulse-slow 8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Enhanced Scrollbar */
::-webkit-scrollbar {
  width: 6px;
}

::-webkit-scrollbar-track {
  background: transparent;
}

::-webkit-scrollbar-thumb {
  background: #E2E8F0;
  border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
  background: #CBD5E0;
}

/* Enhanced transitions */
* {
  transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}
</style>
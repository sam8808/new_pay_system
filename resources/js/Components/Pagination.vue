<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
    links: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <div class="flex items-center justify-between">
        <div class="flex flex-1 justify-between sm:hidden">
            <!-- Мобильная версия -->
            <Link
                v-for="link in links.filter((link) => link.url !== null)"
                :key="link.url"
                :href="link.url"
                :class="[
                    'relative inline-flex items-center rounded-md px-4 py-2 text-sm font-medium',
                    {
                        'border-violet-500 bg-violet-50 text-violet-600':
                            link.active,
                        'border-gray-300 bg-white text-gray-700 hover:bg-gray-50':
                            !link.active,
                    },
                ]"
                v-html="link.label"
            />
        </div>

        <!-- Десктопная версия -->
        <div
            class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"
        >
            <div>
                <nav
                    class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                    aria-label="Pagination"
                >
                    <template v-for="link in links" :key="link.label">
                        <!-- Для активных ссылок -->
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            :class="[
                                'relative inline-flex items-center px-4 py-2 text-sm font-medium focus:z-20',
                                {
                                    'z-10 bg-violet-50 border-violet-500 text-violet-600':
                                        link.active,
                                    'bg-white border-gray-300 text-gray-500 hover:bg-gray-50':
                                        !link.active,
                                },
                                'focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2',
                            ]"
                            v-html="link.label"
                        />
                        <!-- Для неактивных ссылок -->
                        <span
                            v-else
                            :class="[
                                'relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-300 bg-gray-50',
                                'cursor-not-allowed',
                            ]"
                            v-html="link.label"
                        />
                    </template>
                </nav>
            </div>
        </div>
    </div>
</template>

<style scoped>
.pagination-active {
    @apply z-10 bg-violet-50 border-violet-500 text-violet-600;
}

.pagination-inactive {
    @apply bg-white border-gray-300 text-gray-500 hover:bg-gray-50;
}

/* Убираем подчеркивание в ссылках пагинации */
:deep(a) {
    text-decoration: none;
}
</style>

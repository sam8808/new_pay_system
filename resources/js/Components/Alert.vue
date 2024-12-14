<script setup>
import { ref } from "vue";

defineProps({
    message: {
        type: String,
        default: null,
    },
    status: {
        type: String,
        default: null,
    },
});

const openAlert = ref(true);

const closeAlert = () => {
    openAlert.value = false;
};
</script>

<template>
    <div v-if="openAlert" class="fixed top-4 right-0 z-10 flex w-full max-w-sm">
        <div
            v-if="message"
            class="flex overflow-hidden bg-white rounded-lg shadow-md"
        >
            <div
                :class="{
                    'bg-red-600': status === 'error',
                    'bg-green-600': status === 'success',
                }"
                class="w-2"
            ></div>

            <div
                :class="{
                    'text-red-600': status === 'error',
                    'text-green-600': status === 'success',
                }"
                class="flex items-center px-2 py-3"
            >
                <!-- <IconFail v-if="status === 'error'" />
                <IconSuccess v-else-if="status === 'success'" /> -->

                <div class="mx-5">
                    <p
                        :class="{
                            'text-red-600': status === 'error',
                            'text-green-600': status === 'success',
                        }"
                    >
                        {{ message }}
                    </p>
                </div>
                <button
                    class="bg-transparent text-2xl mb-1 font-semibold leading-none outline-none focus:outline-none"
                    @click="closeAlert()"
                >
                    <span>×</span>
                </button>
            </div>
        </div>
    </div>
</template>
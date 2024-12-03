<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Layout from "@/Layouts/AccountLayout.vue";

defineOptions({
  layout: Layout
})

const props = defineProps({
    user: Object,
});

const form = useForm({
    account: props.user.account,
    email: props.user.email,
    telegram: props.user.telegram,
    phone: props.user.phone,
    password: "",
});

const saved = ref(false);
const showDeleteModal = ref(false);

const updateProfile = () => {
    form.put(route("profile.update"), {
        onSuccess: () => {
            saved.value = true;
            setTimeout(() => (saved.value = false), 3000);
        },
    });
};

const confirmDelete = () => {
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    form.reset();
};

const deleteAccount = () => {
    form.delete(route("profile.destroy"), {
        onSuccess: () => {
            showDeleteModal.value = false;
        },
    });
};
</script>

<template>
    <AccountLayout>
        <div class="flex flex-col min-h-screen">
            <div class="p-4 sm:p-8 bg-white shadow">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900">
                        Profile Information
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Update your account's profile information.
                    </p>

                    <form
                        @submit.prevent="updateProfile"
                        class="mt-6 space-y-6"
                    >
                        <div>
                            <div class="text-sm font-medium text-gray-700">
                                Account ID
                            </div>
                            <input
                                disabled
                                type="text"
                                :value="user.account"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm disabled:bg-gray-100"
                            />
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700"
                                >Email</label
                            >
                            <input
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            />
                            <div
                                v-if="form.errors.email"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.email }}
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700"
                                >Telegram</label
                            >
                            <input
                                v-model="form.telegram"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            />
                            <div
                                v-if="form.errors.telegram"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.telegram }}
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700"
                                >Phone</label
                            >
                            <input
                                v-model="form.phone"
                                type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            />
                            <div
                                v-if="form.errors.phone"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.phone }}
                            </div>
                        </div>

                        <div
                            v-if="!user.is_verified"
                            class="text-sm text-yellow-600"
                        >
                            Your email is not verified. Please verify your
                            email.
                        </div>

                        <div class="flex items-center gap-4">
                            <button
                                type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-700"
                            >
                                Save
                            </button>
                            <p v-if="saved" class="text-sm text-gray-600">
                                Saved.
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow mt-6">
                <div class="max-w-xl">
                    <h2 class="text-lg font-medium text-gray-900">
                        Delete Account
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Once your account is deleted, all of its resources and
                        data will be permanently deleted.
                    </p>

                    <button
                        @click="confirmDelete"
                        class="mt-6 inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-red-500"
                    >
                        Delete Account
                    </button>

                    <div
                        v-if="showDeleteModal"
                        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"
                    >
                        <div class="bg-white p-6 rounded-lg max-w-md w-full">
                            <h3 class="text-lg font-medium text-gray-900">
                                Are you sure?
                            </h3>
                            <p class="mt-2 text-sm text-gray-600">
                                This action cannot be undone. Please enter your
                                password to confirm.
                            </p>

                            <input
                                v-model="password"
                                type="password"
                                class="mt-4 block w-full rounded-md border-gray-300 shadow-sm"
                                placeholder="Password"
                            />
                            <div
                                v-if="form.errors.password"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.password }}
                            </div>

                            <div class="mt-6 flex justify-end gap-4">
                                <button
                                    @click="closeDeleteModal"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-500"
                                >
                                    Cancel
                                </button>
                                <button
                                    @click="deleteAccount"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-500"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AccountLayout>
</template>

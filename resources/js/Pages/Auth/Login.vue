<script setup>
import { reactive, ref, defineProps } from "vue";
import FormInput from "../../Components/FormInput.vue";
import { MiLoginHalfCircle, CiMail, CiLock } from "@kalimahapps/vue-icons";
import { router, usePage } from "@inertiajs/vue3";
import Alert from "../../Components/Alert.vue";
defineProps({
    errors: {
        type: Object,
    },
});

const page = usePage();

const companyName = "Payment System";
const loading = ref(false);
const show = ref(false);

const code1 = ref('');
const code2 = ref('');
const code3 = ref('');
const code4 = ref('');
const code5 = ref('');
const code6 = ref('');

const twoFaError = ref('');

const form = reactive({
    email: "",
    password: "",
});

const submit = () => {
    loading.value = true;
    router.post(route("login.store"), form, {
        preserveScroll: true,
        onFinish: () => {
            loading.value = false;
        },
    });
};

const focusNext = (event, nextInput) => {
    if (event.target.value.length === 1) {
        const nextElement = document.getElementById(nextInput);
        if (nextElement) {
            nextElement.focus();
        }
    }
};

/**
 * Send 2FA code
 * */
const process2FA = () => {
    fetch(route("2fa.send"), {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
        },
        body: JSON.stringify({ form }),
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            // Assuming the server returns a JSON response
            if (data.sent) {
                show.value = true; // Handle success
            } else {
                console.error("Failed to send 2FA code");
            }
        })
        .catch((error) => {
            console.error("There was a problem with the fetch operation:", error);
        });

};

const verify2FA = () => {
    const code = code1.value + code2.value + code3.value + code4.value + code5.value + code6.value;

    fetch(route("2fa.verify"), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ code: code, form : form }),
    })
        .then(response => response.json())
        .then(data => {
            // Handle response data
            if(data.verified){
                submit()
            }else{
                twoFaError.value = 'Wrong code!'
            }

        })
        .finally(() => {
            loading.value = false;
        });

};
</script>

<template>
    <div v-show="!show"
        class="login-form min-h-screen bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-blue-100 via-violet-100 to-gray-100 flex flex-col justify-center items-center p-6"
    >
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div
                class="absolute top-1/4 left-1/4 w-96 h-96 bg-gradient-to-r from-blue-400/30 to-indigo-400/30 rounded-full filter blur-3xl"
            ></div>
            <div
                class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-gradient-to-r from-violet-400/30 to-purple-400/30 rounded-full filter blur-3xl"
            ></div>
        </div>

        <div class="relative w-full max-w-md">
            <div
                class="bg-white/80 backdrop-blur-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-3xl p-8 border border-white/20"
            >
                <div class="mb-10 space-y-2">
                    <h1
                        class="text-4xl font-bold text-center bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 bg-clip-text text-transparent"
                    >
                        Добро пожаловать
                    </h1>
                    <p class="text-gray-500 text-center font-medium">
                        Войдите в аккаунт
                    </p>
                </div>

                <!-- Form section -->
                <form @submit.prevent="process2FA" class="space-y-4">
                    <FormInput
                        v-model="form.email"
                        title="Email"
                        type="email"
                        :message="errors.email"
                        :icon="CiMail"
                        placeholder="Введите email"
                        autocomplete="email"
                    />

                    <FormInput
                        v-model="form.password"
                        title="Пароль"
                        type="password"
                        :message="errors.password"
                        :icon="CiLock"
                        placeholder="Введите пароль"
                        :minLength="8"
                        autocomplete="new-password"
                    />

                    <button
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-violet-600 text-white py-3.5 rounded-xl font-bold hover:from-blue-700 hover:via-indigo-700 hover:to-violet-700 transform hover:-translate-y-0.5 transition duration-200 ease-in-out shadow-[0_4px_20px_rgba(0,0,0,0.1)] hover:shadow-[0_8px_30px_rgba(0,0,0,0.15)] active:transform-none disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:transform-none disabled:hover:shadow-[0_4px_20px_rgba(0,0,0,0.1)] flex justify-center items-center space-x-2"
                    >
                        <span>Войти</span>
                        <component
                            :is="MiLoginHalfCircle"
                            class="w-5 h-5"
                            v-if="!loading"
                        />
                        <svg
                            v-if="loading"
                            class="animate-spin h-5 w-5"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            />
                        </svg>
                    </button>

                    <div class="text-center">
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-700 font-medium transition duration-200"
                        >
                            <span>←</span>
                            <span>У вас нет аккаунта? Регистрация</span>
                        </Link>
                    </div>

                    <div class="text-center">
                        <Link
                            :href="route('home')"
                            class="inline-flex items-center space-x-1 text-blue-600 hover:text-blue-700 font-medium transition duration-200"
                        >
                            <span>На главную</span>
                        </Link>
                    </div>
                </form>
            </div>

            <div
                class="absolute -top-2 -right-2 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full shadow-lg border border-white/20 flex items-center space-x-1"
            >
                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                <span class="text-xs font-medium text-gray-600"
                    >Защищено SSL</span
                >
            </div>
        </div>

        <!-- Footer section -->
        <div class="mt-8 text-center space-y-4">
            <div class="flex justify-center space-x-6">
                <div class="flex items-center space-x-1">
                    <svg
                        class="w-5 h-5 text-yellow-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                    </svg>
                    <svg
                        class="w-5 h-5 text-yellow-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                    </svg>
                    <svg
                        class="w-5 h-5 text-yellow-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                    </svg>
                    <svg
                        class="w-5 h-5 text-yellow-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                    </svg>
                    <svg
                        class="w-5 h-5 text-yellow-400"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"
                        />
                    </svg>
                    <span class="text-sm font-medium text-gray-500 ml-2"
                        >4.9/5 от наших пользователей</span
                    >
                </div>
            </div>

            <!-- Trust badges -->
            <div class="flex justify-center items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <svg
                        class="w-5 h-5 text-green-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                        />
                    </svg>
                    <span class="text-sm text-gray-500"
                        >Безопасная регистрация</span
                    >
                </div>
                <div class="flex items-center space-x-2">
                    <svg
                        class="w-5 h-5 text-blue-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                        />
                    </svg>
                    <span class="text-sm text-gray-500">Защита данных</span>
                </div>
                <div class="flex items-center space-x-2">
                    <svg
                        class="w-5 h-5 text-purple-500"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z"
                        />
                    </svg>
                    <span class="text-sm text-gray-500">Быстрая поддержка</span>
                </div>
            </div>

            <!-- Copyright -->
            <p class="text-sm text-gray-500">
                © 2024 {{ companyName }}. Все права защищены.
            </p>
        </div>
    </div>

    <div
        v-show="show"
        class="min-h-screen bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))]
         from-blue-100 via-violet-100 to-gray-100 flex flex-col justify-center items-center p-6"
    >
        <div class="flex flex-col items-center mb-6">
            <!-- Add Illustration -->
            <div class="text-blue-500 text-6xl mb-4">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1 class="text-2xl font-semibold text-gray-700 text-center">
                Two-Factor Authentication
            </h1>
            <p class="text-gray-500 text-center mt-2">
                Please enter the 6-digit code sent to your email or phone.
            </p>
        </div>

        <form class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
            <!-- Input Group for 2FA Code -->
            <div class="twofa-input-group flex justify-between mb-6">
                <input
                    type="text"
                    maxlength="1"
                    v-model="code1"
                    id="code1"
                    @input="focusNext($event, 'code2')"
                    class="w-12 h-12 rounded-md border-2 text-center text-xl
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                     border-gray-300 transition"
                />
                <input
                    type="text"
                    maxlength="1"
                    v-model="code2"
                    id="code2"
                    @input="focusNext($event, 'code3')"
                    class="w-12 h-12 rounded-md border-2 text-center text-xl
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                     border-gray-300 transition"
                />
                <input
                    type="text"
                    maxlength="1"
                    v-model="code3"
                    id="code3"
                    @input="focusNext($event, 'code4')"
                    class="w-12 h-12 rounded-md border-2 text-center text-xl
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                     border-gray-300 transition"
                />
                <input
                    type="text"
                    maxlength="1"
                    v-model="code4"
                    id="code4"
                    @input="focusNext($event, 'code5')"
                    class="w-12 h-12 rounded-md border-2 text-center text-xl
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                     border-gray-300 transition"
                />
                <input
                    type="text"
                    maxlength="1"
                    v-model="code5"
                    id="code5"
                    @input="focusNext($event, 'code6')"
                    class="w-12 h-12 rounded-md border-2 text-center text-xl
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                     border-gray-300 transition"
                />
                <input
                    type="text"
                    maxlength="1"
                    v-model="code6"
                    id="code6"
                    class="w-12 h-12 rounded-md border-2 text-center text-xl
                     focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                     border-gray-300 transition"
                />
            </div>
            <h5 class="errorMessage">{{twoFaError}}</h5>
            <!-- Submit Button -->
            <button
                @click="verify2FA"
                type="button"
                class="w-full mt-2 bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600
                 transition duration-200 text-lg font-medium flex items-center justify-center"
            >
                <i class="fas fa-check-circle mr-2"></i>
                Verify
            </button>
        </form>
    </div>

</template>
<style>
.errorMessage{
    color : red;
}
</style>

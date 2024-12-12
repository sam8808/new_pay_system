<script setup>
import { ref } from 'vue';
import { router, usePage } from "@inertiajs/vue3";

const { props } = usePage()

// Define reactive references
const couponCode = ref('');
const showModal = ref(false); // Modal visibility control
const modalMessage = ref(''); // Store the response message

const useCoupon = async () => {
    console.log('Coupon Code:', couponCode.value); // Access the value using .value
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const response = await fetch("/merchant-coupon/verify", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken, // Add the CSRF token here
            },
            body: JSON.stringify({
                merchant_id: props.merchant_id,
                code: couponCode.value, // Use the entered coupon code
            }),
        });

        if (!response.ok) {
            throw new Error("Failed to use coupon.");
        }

        const result = await response.json();
        console.log(result);

        // Format response for user-friendly display
        modalMessage.value = `
            Coupon Redeemed Successfully!
            Thank you for using your coupon!
        `;
        showModal.value = true; // Show the modal
    } catch (error) {
        console.error(error);
        modalMessage.value = "There was an error redeeming the coupon."; // Error message
        showModal.value = true; // Show the modal
    }
};
</script>

<template>
    <div
        class="bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-500 p-6 space-y-6 rounded-lg shadow-lg max-w-4xl mx-auto">
        <div class="text-white flex justify-center items-center">
            <img src="https://previews.123rf.com/images/chanut/chanut1808/chanut180802000/107443790-two-sale-coupons-vector-illustration-in-line-color-design.jpg"
                alt="Coupon Image" class="rounded-full shadow-md mb-4" />
        </div>

        <ul class="text-xl flex justify-center gap-6 mb-6">
            <Link :href="route('test.merchant', { merchant_id: props.merchant_id })" class="group">
            Shop
            </Link>
            <Link :href="route('merchant-coupon.create', { merchant_id: props.merchant_id })" class="group">
            Create coupon
            </Link>
            <Link :href="route('merchant-coupon.use', { merchant_id: props.merchant_id })" class="group">
            Use coupon
            </Link>
        </ul>

        <hr class="border-gray-300 mb-6">

        <div class="text-center text-white">
            <h1 class="text-3xl font-semibold mb-4">Use Your Coupon Here!</h1>
            <p class="text-lg mb-6">Enter your coupon code below to redeem your offer</p>

            <form>
                <div class="space-y-12">
                    <div class="border-b border-gray-900/10 pb-12">
                        <div class="mt-10 flex flex-col items-center gap-y-6">
                            <!-- Label and Input -->
                            <div class="w-full max-w-md">
                                <label for="coupon-code"
                                    class="block text-sm font-medium text-gray-900 text-center">Coupon Code</label>
                                <div class="mt-2">
                                    <input type="text" name="coupon-code" id="coupon-code" v-model="couponCode"
                                        autocomplete="off"
                                        class="block w-full rounded-md bg-white px-4 py-2 text-lg text-gray-900 outline-none shadow-sm focus:ring-2 focus:ring-indigo-600 focus:border-indigo-500 placeholder:text-gray-400">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-center gap-x-6">
                    <button type="button" @click="useCoupon"
                        class="rounded-md bg-indigo-600 px-6 py-3 text-lg font-semibold text-white shadow-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-all">Use
                        Coupon</button>
                </div>
            </form>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-6 rounded-lg w-1/3 text-center">
                <div class="text-left">{{ modalMessage }}</div> <!-- Display formatted message -->
                <button @click="showModal = false"
                    class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md">Close</button>
            </div>
        </div>
    </div>
</template>

<style scoped>
body {
    font-family: 'Roboto', sans-serif;
}

.bg-gradient-to-r {
    background-size: cover;
    background-position: center;
}

img {
    width: 100%;
    height: auto;
    max-width: 150px;
    object-fit: cover;
}
</style>

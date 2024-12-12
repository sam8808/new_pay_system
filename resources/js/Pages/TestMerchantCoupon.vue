<script setup>
import { ref } from 'vue';
import { router, usePage } from "@inertiajs/vue3";

const { props } = usePage()

// Define couponAmount as a reactive reference
const couponAmount = ref(10); // Default value can be 10 or any other number
const showModal = ref(false); // Modal visibility control
const modalMessage = ref(''); // Store the response message

const createCoupon = async () => {
    console.log('Coupon Amount:', couponAmount.value); // Access the value using .value
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const response = await fetch("/merchant-coupon", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken, // Add the CSRF token here
            },
            body: JSON.stringify({
                merchant_id: props.merchant_id,
                amount: couponAmount.value, // Using the dynamic amount
                currency: 'USD',
                description: 'Payment from test merchant',
                gateway_id: 1
            }),
        });

        if (!response.ok) {
            throw new Error("Payment failed.");
        }

        const result = await response.json();
        console.log(result);

        // Format response for user-friendly display
        modalMessage.value = `
            Coupon Created Successfully!

            Coupon Amount: $${result.data.amount}
            Coupon Code: ${result.data.code}

            Terms of Use: ${result.data.terms_of_use}

            Expires At: ${new Date(result.data.expires_at).toLocaleString()}
        `;

        showModal.value = true; // Show the modal
    } catch (error) {
        console.error(error);
        modalMessage.value = "There was an error creating the coupon."; // Error message
        showModal.value = true; // Show the modal
    }
};
</script>

<template>
    <div
        class="bg-gradient-to-r from-green-400 via-blue-500 to-purple-600 p-8 space-y-6 rounded-lg shadow-lg max-w-4xl mx-auto">
        <div class="text-white flex justify-center items-center mb-6">
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
            <h1 class="text-3xl font-semibold mb-4">Create Your Coupon</h1>
            <p class="text-lg mb-6">Enter the coupon amount below to create a new coupon</p>

            <form>
                <div class="space-y-12">
                    <div class="border-b border-gray-900/10 pb-12">
                        <div class="mt-10 flex flex-col items-center gap-y-6">
                            <!-- Label and Input -->
                            <div class="w-full max-w-md">
                                <label for="coupon-amount"
                                    class="block text-sm font-medium text-gray-900 text-center">Coupon Amount</label>
                                <div class="mt-2">
                                    <input type="number" name="coupon-amount" id="coupon-amount" v-model="couponAmount"
                                        autocomplete="off"
                                        class="block w-full rounded-md bg-white px-4 py-2 text-lg text-gray-900 outline-none shadow-sm focus:ring-2 focus:ring-indigo-600 focus:border-indigo-500 placeholder:text-gray-400">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-center gap-x-6">
                    <button type="button" @click="createCoupon"
                        class="rounded-md bg-indigo-600 px-6 py-3 text-lg font-semibold text-white shadow-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-all">Create
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

.hover\:text-yellow-400:hover {
    color: #fbbf24;
}
</style>

<script setup>
import { ref } from 'vue';

const props = defineProps({
    merchant_id: {
        type: String,
        required: true,
    },
    homePageRoute: String,
    createCouponPageRoute: String,
    useCouponPageRoute: String
});

// Define couponAmount as a reactive reference
const couponCode = ref(''); // Default value can be 10 or any other number

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
                amount: 100,
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
    } catch (error) {
        console.error(error);
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

        <ul class="text-xl  flex justify-center gap-6 mb-6">
            <Link :href="route('test.merchant')" class="group">
            Shop
            </Link>
            <Link :href="route('merchant-coupon.create')" class="group">
            Create coupon
            </Link>
            <Link :href="route('merchant-coupon.use')" class="group">
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
                    <button type="button" @click="createCoupon"
                        class="rounded-md bg-indigo-600 px-6 py-3 text-lg font-semibold text-white shadow-lg hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-600 transition-all">Create
                        Coupon</button>
                </div>
            </form>
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

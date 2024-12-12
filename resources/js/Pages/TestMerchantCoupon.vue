<script setup>
import { ref } from 'vue';

const props = defineProps({
    merchant_id: {
        type: String,
        required: true,
    },
    homePageRoute: String,
    createCouponPageRoute: String,
});

// Define couponAmount as a reactive reference
const couponAmount = ref(10); // Default value can be 10 or any other number

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
    <div class="p-6 space-y-6">
        <ul class="text-2xl" style="display: flex; gap: 2%">
            <li><a :href="props.homePageRoute">Shop</a></li>
            <li><a :href="props.createCouponPageRoute">Create a coupon</a></li>
        </ul>
        <hr>

        <div class="text-center text-gray-100">
            <h1 class="text-2xl text-black font-bold mb-2">In this page you can create a coupon</h1>
            <form>
                <div class="space-y-12">
                    <div class="border-b border-gray-900/10 pb-12">
                        <div class="mt-10 flex flex-col items-center gap-y-4">
                            <!-- Label and Input -->
                            <div class="w-1/2">
                                <label for="coupon-amount"
                                    class="block text-sm font-medium text-gray-900 text-center">Coupon Amount</label>
                                <div class="mt-2">
                                    <input type="number" name="coupon-amount" id="coupon-amount" v-model="couponAmount"
                                        autocomplete="off"
                                        class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-x-6">
                    <button type="button" class="text-sm font-semibold text-gray-900">Cancel</button>
                    <button type="button" @click="createCoupon"
                        class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Create</button>
                </div>
            </form>
        </div>
    </div>
</template>

<style></style>

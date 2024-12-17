<script setup>
import { ref } from "vue";
import { usePage } from "@inertiajs/vue3";

const { props } = usePage()

// Sample products
const products = [
    {
        id: 1,
        title: "Phone 1",
        description: "The Phone 1 offers a sleek design, high performance, and great value. It's equipped with a sharp 6.5-inch display, a powerful processor, and a long-lasting battery to keep you going throughout the day.",
        price: 49.99,
        image: "https://images.unsplash.com/photo-1616410011236-7a42121dd981?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    },
    {
        id: 2,
        title: "Phone 2",
        description: "Experience the perfect balance of functionality and design with Phone 2. Featuring an upgraded camera, faster processor, and a sleek, ergonomic build, it’s the ideal companion for everyday use and entertainment.",
        price: 79.99,
        image: "https://images.unsplash.com/photo-1615790166084-59ace2ae5ba2?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    },
    {
        id: 3,
        title: "Phone 3",
        description: "With a stunning 108MP camera, 5G support, and a cutting-edge OLED display, Phone 3 redefines mobile technology. Whether you're streaming, gaming, or snapping photos, it's built to elevate your experience.",
        price: 99.99,
        image: "https://images.unsplash.com/photo-1616865609199-abb1465abf5c?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    },
    {
        id: 4,
        title: "Phone 4",
        description: "The Phone 4 brings cutting-edge performance with its fast Snapdragon processor, incredible 120Hz refresh rate display, and exceptional battery life. Ideal for heavy users who need speed and efficiency.",
        price: 149.99,
        image: "https://images.unsplash.com/photo-1621771834698-67f46aa51da8?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    },
    {
        id: 5,
        title: "Phone 5",
        description: "Phone 5 features a 5G-ready chipset and a professional-grade camera system with ultra-wide and macro lenses. Whether for professional photography or gaming, this phone offers unmatched versatility.",
        price: 199.99,
        image: "https://images.unsplash.com/photo-1611734828917-718f25babb2b?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    },
    {
        id: 6,
        title: "Phone 6",
        description: "This high-end flagship phone offers the best in class with a stunning AMOLED display, exceptional camera quality with night mode, and a powerful chipset capable of handling any task without breaking a sweat.",
        price: 299.99,
        image: "https://images.unsplash.com/photo-1616410072514-e92114cf1a88?q=80&w=1932&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D",
    },
];

// State for feedback
const loading = ref(false);
const successMessage = ref("");
const amount = ref(100);

// Function to handle payment
const handlePayment = async (product) => {
    loading.value = true;
    successMessage.value = ""; // Reset success message

    try {
        const response = await fetch("/api/payments/create", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                merchant_id: props.merchant_id ?? 1,
                productId: product.id,
                productTitle: product.title,
                amount: product.price,
                currency_id: 1,
                description: 'Payment from test merchant',
                gateway_id : 1
            }),
        });

        if (!response.ok) {
            throw new Error("Payment failed.");
        }

        const result = await response.json();

        if (!result.original.payment_link) {
            throw new Error("Payment failed.");
        }
        const paymentLink = result.original.payment_link;
        window.open(paymentLink, '_blank');

        successMessage.value = ``;
    } catch (error) {
        console.error(error);
        alert(error.message);
        successMessage.value = "";
    } finally {
        loading.value = false;
    }
};

</script>

<template>
    <div class="bg-gradient-to-r from-green-400 via-blue-500 to-purple-600 p-8 space-y-6 rounded-lg shadow-lg mx-auto">

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

        <!-- Description Section -->
        <div class="text-center mb-6">
            <h1 class="text-3xl font-semibold mb-4">Explore Our Products</h1>
            <p class="text-lg">Select a product to learn more and proceed to checkout when you're ready.</p>
        </div>

        <!-- Product Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            <div v-for="(product, index) in products" :key="product.id"
                class="relative p-6 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:scale-105">
                <!-- Product Image -->
                <div class="w-full h-48 bg-gray-200 rounded-xl overflow-hidden relative">
                    <img :src="product.image" :alt="product.title"
                        class="w-full h-full object-cover transition-transform duration-300" />
                </div>

                <!-- Product Details -->
                <div class="mt-4 text-black">
                    <h2
                        class="text-2xl font-semibold text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-red-600">
                        {{ product.title }}
                    </h2>
                    <p class="text-sm mt-2">
                        {{ product.description }}
                    </p>
                </div>

                <!-- Price and Actions -->
                <div class="mt-4 flex flex-col items-center justify-between">
                    <input type="text" v-model="amount" >
                    <button @click="handlePayment(product)"
                        class="w-full mt-4 text-center text-sm font-medium  bg-emerald-500 hover:bg-emerald-600 px-6 py-3 rounded-xl transition-all"
                        :disabled="loading">
                        {{ `Pay $${amount}` }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="mt-4 text-center text-green-500 font-semibold">
            {{ successMessage }}
        </div>
    </div>
</template>

<style scoped>
.backdrop-blur-xl {
    backdrop-filter: blur(24px);
}

.bg-gradient-to-r {
    background: linear-gradient(to right, #4ade80, #34d399);
}

.hover\:text-yellow-400:hover {
    color: #fbbf24;
}

.text-center {
    text-align: center;
}

.bg-emerald-500 {
    background-color: #34d399;
}

.bg-emerald-600 {
    background-color: #10b981;
}

.hover\:bg-emerald-600:hover {
    background-color: #10b981;
}

.text-transparent {
    color: transparent;
}

.bg-clip-text {
    background-clip: text;
}

.transition-all {
    transition: all 0.3s ease;
}
</style>

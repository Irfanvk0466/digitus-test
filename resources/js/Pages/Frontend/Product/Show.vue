<script setup>
import { computed, ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import { ShoppingCartIcon, ArrowLeftIcon } from "@heroicons/vue/24/solid";

const props = defineProps({
  product: Object,
  hasPendingOrder: Boolean,
});

const isLoading = ref(false);

const buyNow = () => {
  if (!props.product) {
    alert("Product not found!");
    return;
  }

  if (props.hasPendingOrder) {
    alert("This product has a pending order!");
    return;
  }

  isLoading.value = true;
  router.visit(`/payment/${props.product.id}`, {
    onFinish: () => (isLoading.value = false),
  });
};

const buyNowButtonClass = computed(() => ({
  "bg-green-500 hover:bg-green-600": !props.hasPendingOrder,
  "bg-gray-400 cursor-not-allowed": props.hasPendingOrder,
  "text-white px-4 py-2 rounded shadow flex items-center space-x-2": true,
}));
</script>

<template>
  <AppLayout title="Product Details">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Product Details
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          
          <!-- Product Details -->
          <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">{{ product.name }}</h1>
          </div>

          <table class="w-full border-collapse border">
            <thead>
              <tr class="bg-gray-200">
                <th class="border px-4 py-2 text-left">Attribute</th>
                <th class="border px-4 py-2 text-left">Details</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border">
                <td class="border px-4 py-2 font-semibold">Product Name</td>
                <td class="border px-4 py-2">{{ product.name }}</td>
              </tr>
              <tr class="border">
                <td class="border px-4 py-2 font-semibold">Price</td>
                <td class="border px-4 py-2">₹{{ product.price }}</td>
              </tr>
              <tr class="border">
                <td class="border px-4 py-2 font-semibold">Brand</td>
                <td class="border px-4 py-2">{{ product.brand?.name || "N/A" }}</td>
              </tr>
            </tbody>
          </table>

          <!-- Action Buttons -->
          <div class="flex justify-between items-center mt-6">
            <Link href="/products" class="bg-gray-500 text-white px-4 py-2 rounded shadow flex items-center space-x-2">
              <ArrowLeftIcon class="w-5 h-5" />
              <span>Back to Products</span>
            </Link>

            <button
              @click="buyNow"
              :disabled="hasPendingOrder || isLoading"
              :class="buyNowButtonClass"
            >
              <ShoppingCartIcon class="w-5 h-5" />
              <span v-if="!isLoading">{{ hasPendingOrder ? 'Pending Order' : 'Buy Now' }}</span>
              <span v-else>Processing...</span>
            </button>
          </div>
          
        </div>
      </div>
    </div>
  </AppLayout>
</template>

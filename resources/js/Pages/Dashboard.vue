<script setup>
import { ref, computed } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { router } from "@inertiajs/vue3";

const props = defineProps({
  products: Object,
});

const searchQuery = ref("");

const filteredProducts = computed(() => {
  return searchQuery.value
    ? props.products.data.filter((product) =>
        product.name.toLowerCase().includes(searchQuery.value.toLowerCase())
      )
    : props.products.data;
});

//pagination
function goToPage(url) {
  if (url) router.visit(url);
}

//to get product details by id
function goToProductDetails(productId) {
  router.visit(`/products/${productId}`);
}
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Product Listing</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Title & Search Bar in Same Line -->
        <div class="flex justify-between items-center mb-4">
          <h1 class="text-2xl font-bold">Available Products</h1>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search products..."
            class="w-full sm:w-1/3 p-2 border rounded-lg shadow-sm"
          />
        </div>

        <div v-if="filteredProducts.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          <div
            v-for="product in filteredProducts"
            :key="product.id"
            class="bg-white shadow-lg rounded-lg overflow-hidden p-4"
          >
            <h3 class="text-xl font-semibold text-gray-800">{{ product.name }}</h3>
            <p class="text-lg text-gray-600 mt-2">₹{{ product.price }}</p>
            <p class="text-sm text-gray-500 mt-2">Brand: {{ product.brand?.name || "N/A" }}</p>

            <div class="mt-4">
              <button
                @click="goToProductDetails(product.id)"
                class="w-full bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition"
              >
                Know More
              </button>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-500 text-center mt-4">No products found.</p>

        <!-- Pagination -->
        <div class="mt-4 flex justify-between items-center">
          <span class="text-sm text-gray-700">
            Showing {{ props.products.from }} to {{ props.products.to }} of {{ props.products.total }} entries
          </span>
          <div class="flex space-x-2">
            <button
              v-for="link in props.products.links"
              :key="link.label"
              @click="goToPage(link.url)"
              :class="{
                'bg-blue-500 text-white': link.active,
                'bg-gray-200 text-gray-700': !link.active,
              }"
              class="px-4 py-2 rounded shadow"
              v-html="link.label"
              :disabled="!link.url"
            ></button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>


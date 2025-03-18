<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({ product: Object, brands: Array });

// Use props.product to initialize form values
const form = ref({
  name: props.product.name, 
  price: props.product.price, 
  brand_id: props.product.brand_id,
});

const errors = ref({});

// Function to submit form
function submit() {
  router.put(`/products/${props.product.id}`, form.value, {
    onSuccess: () => {
      errors.value = {}; // Clear errors after a successful update
    },
    onError: (err) => {
      errors.value = err; // Capture errors from Laravel validation
    },
  });
}
</script>


<template>
  <AppLayout title="Edit Product">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Product</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h1 class="text-2xl font-bold mb-4">Update Product</h1>

          <form @submit.prevent="submit">
            <!-- Product Name -->
            <div class="mb-4">
              <label class="block text-gray-700 font-bold mb-2">Product Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border px-4 py-2 rounded"
                placeholder="Enter product name"
                :class="{ 'border-red-500': errors.name }"
              />
              <p v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</p>
            </div>

            <!-- Product Price -->
            <div class="mb-4">
              <label class="block text-gray-700 font-bold mb-2">Price</label>
              <input
                v-model="form.price"
                type="number"
                class="w-full border px-4 py-2 rounded"
                placeholder="Enter product price"
                :class="{ 'border-red-500': errors.price }"
              />
              <p v-if="errors.price" class="text-red-500 text-sm">{{ errors.price }}</p>
            </div>

            <!-- Brand Selection -->
            <div class="mb-4">
              <label class="block text-gray-700 font-bold mb-2">Brand</label>
              <select
                v-model="form.brand_id"
                class="w-full border px-4 py-2 rounded"
                :class="{ 'border-red-500': errors.brand_id }"
              >
                <option value="">Select a brand</option>
                <option v-for="brand in brands" :key="brand.id" :value="brand.id">
                  {{ brand.name }}
                </option>
              </select>
              <p v-if="errors.brand_id" class="text-red-500 text-sm">{{ errors.brand_id }}</p>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">
              <Link href="/products" class="bg-black text-white px-4 py-2 rounded shadow">
                Back to Products
              </Link>
              <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded shadow">
                Update Product
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

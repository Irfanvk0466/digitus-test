<script setup>
import { ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import {
  PencilIcon, TrashIcon, ArrowDownTrayIcon, PaperClipIcon, PlusIcon, EyeIcon
} from "@heroicons/vue/24/solid";

defineProps({ products: Object, brands: Array });

const file = ref(null);
const errors = ref({});
const isModalOpen = ref(false);

// Function to handle file upload
function uploadFile() {
  if (!file.value) {
    alert("Please select a file first.");
    return;
  }

  const formData = new FormData();
  formData.append("file", file.value);

  router.post("/import", formData, {
    onSuccess: () => {
      alert("Products imported successfully!");
      file.value = null;
      errors.value = {};
      isModalOpen.value = false;
    },
    onError: (err) => {
      errors.value = err;
    },
  });
}

// Handle file input change
function handleFileChange(event) {
  file.value = event.target.files[0];
}

// Function to delete a product
function deleteProduct(productId) {
  if (confirm("Are you sure you want to delete this product?")) {
    router.delete(`/products/${productId}`, {
      onSuccess: () => alert("Product deleted successfully!"),
    });
  }
}

//for export download
function exportProducts() {
    window.location.href = "/export";
}

//navigation
function goToPage(url) {
  if (url) {
    router.visit(url);
  }
}
</script>

<template>
  <AppLayout title="Products">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Product List</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Products</h1>
            <div class="flex space-x-4">
              <button @click="isModalOpen = true" class="bg-green-500 text-white px-4 py-2 rounded shadow flex items-center space-x-2">
                <PaperClipIcon class="w-5 h-5" />
                <span>Import</span>
              </button>
              <button @click="exportProducts" class="bg-yellow-500 text-white px-4 py-2 rounded shadow flex items-center space-x-2">
                <ArrowDownTrayIcon class="w-5 h-5" />
                <span>Export</span>
              </button>
              <Link href="/products/create" class="bg-blue-500 text-white px-4 py-2 rounded shadow flex items-center space-x-2">
                <PlusIcon class="w-5 h-5" />
                <span>Add</span>
              </Link>
            </div>
          </div>

          <table class="w-full border-collapse border">
            <thead>
              <tr class="bg-gray-200">
                <th class="border px-4 py-2 text-left">Name</th>
                <th class="border px-4 py-2 text-left">Price</th>
                <th class="border px-4 py-2 text-left">Brand</th>
                <th class="border px-4 py-2 text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="product in products.data" :key="product.id" class="border">
                <td class="border px-4 py-2">{{ product.name }}</td>
                <td class="border px-4 py-2">₹{{ product.price }}</td>
                <td class="border px-4 py-2">{{ product.brand?.name || 'N/A' }}</td>
                <td class="border px-4 py-2 flex space-x-2 justify-center">
                  <Link :href="`/products/${product.id}`">
                    <EyeIcon class="w-6 h-6 text-green-500 hover:text-green-700" />
                  </Link>
                  <Link :href="`/products/${product.id}/edit`">
                    <PencilIcon class="w-6 h-6 text-blue-500 hover:text-blue-700" />
                  </Link>
                  <button @click="deleteProduct(product.id)">
                    <TrashIcon class="w-6 h-6 text-red-500 hover:text-red-700" />
                  </button>
              </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination Controls -->
          <div class="mt-4 flex justify-between items-center">
            <div>
              <span class="text-sm text-gray-700">
                Showing {{ products.from }} to {{ products.to }} of {{ products.total }} entries
              </span>
            </div>
            <div class="flex space-x-2">
              <button
                v-for="link in products.links"
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

          <div v-if="Object.keys(errors).length" class="mt-4 text-red-500">
            <p v-for="(error, index) in errors" :key="index">{{ error }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Import Modal -->
    <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-lg font-bold mb-4">Import Products</h2>
        <input type="file" @change="handleFileChange" accept=".xlsx, .csv" class="w-full border p-2 rounded" />
        <div class="flex justify-end space-x-2 mt-4">
          <button @click="isModalOpen = false" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
          <button @click="uploadFile" class="bg-blue-500 text-white px-4 py-2 rounded">Upload</button>
        </div>
        <div v-if="Object.keys(errors).length" class="mt-4 text-red-500">
          <p v-for="(error, index) in errors" :key="index">{{ error }}</p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
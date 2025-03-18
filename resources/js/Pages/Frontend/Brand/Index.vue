<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import { PencilIcon, TrashIcon ,PlusIcon} from "@heroicons/vue/24/solid";

defineProps({ brands: Object });

// Function to delete a brand
function deleteBrand(brandId) {
  if (confirm("Are you sure you want to delete this brand?")) {
    router.delete(`/brands/${brandId}`, {
      onSuccess: () => alert("Brand deleted successfully!"),
    });
  }
}

// Function to navigate pages
function goToPage(url) {
  if (url) {
    router.visit(url);
  }
}
</script>

<template>
  <AppLayout title="Brands">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Brand List</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Header with Add Button -->
          <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold">Brands</h1>
            <!-- Add Brand Button with Plus Icon -->
            <Link href="/brands/create" class="bg-blue-500 text-white px-4 py-2 rounded shadow flex items-center">
              <PlusIcon class="w-5 h-5 mr-2" /> <!-- Plus Icon -->
              Add
            </Link>
          </div>

          <!-- Brands Table -->
          <table class="w-full border-collapse border">
            <thead>
              <tr class="bg-gray-200">
                <th class="border px-4 py-2">Brand Name</th>
                <th class="border px-4 py-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="brand in brands.data" :key="brand.id" class="border">
                <td class="border px-4 py-2">{{ brand.name }}</td>
                <td class="border px-4 py-2 flex space-x-2">
                  <!-- Edit Icon -->
                  <Link :href="`/brands/${brand.id}/edit`">
                    <PencilIcon class="w-6 h-6 text-blue-500 hover:text-blue-700" />
                  </Link>

                  <!-- Delete Icon -->
                  <button @click="deleteBrand(brand.id)">
                    <TrashIcon class="w-6 h-6 text-red-500 hover:text-red-700" />
                  </button>
                </td>
              </tr>
              <tr v-if="brands.data.length === 0">
                <td colspan="2" class="text-center text-gray-500 p-4">No brands found.</td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination Controls -->
          <div class="mt-4 flex justify-between items-center">
            <div>
              <span class="text-sm text-gray-700">
                Showing {{ brands.from }} to {{ brands.to }} of {{ brands.total }} entries
              </span>
            </div>
            <div class="flex space-x-2">
              <button
                v-for="link in brands.links"
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
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ArrowLeftIcon } from "@heroicons/vue/24/solid";

const form = ref({
  name: "",
});

const errors = ref({});

// Function to submit form
function submit() {
  router.post("/brands", form.value, {
    onSuccess: () => {
      form.value = { name: "" };
      errors.value = {};
    },
    onError: (err) => {
      errors.value = err; 
    },
  });
}
</script>

<template>
  <AppLayout title="Create Brand">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Brand</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <h1 class="text-2xl font-bold mb-4">Add New Brand</h1>

          <form @submit.prevent="submit">
            <!-- Brand Name -->
            <div class="mb-4">
              <label class="block text-gray-700 font-bold mb-2">Brand Name</label>
              <input
                v-model="form.name"
                type="text"
                class="w-full border px-4 py-2 rounded"
                placeholder="Enter brand name"
                :class="{ 'border-red-500': errors.name }"
              />
              <p v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</p>
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center mt-6">
              <Link href="/brands" class="bg-gray-500 text-white px-4 py-2 rounded shadow flex items-center space-x-2">
                <ArrowLeftIcon class="w-5 h-5" />
                <span>Back to Brands</span>
              </Link>
              <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded shadow">
                Save Brand
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

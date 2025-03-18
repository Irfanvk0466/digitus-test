<script setup>
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { TrashIcon } from "@heroicons/vue/24/solid";
import dayjs from "dayjs";

defineProps({ orders: Object });

// Navigate to a given page
function goToPage(url) {
  if (url) {
    router.visit(url);
  }
}

// Cancel an order
function cancelOrder(orderId) {
  if (confirm("Are you sure you want to cancel this order?")) {
    router.post(
      `/orders/${orderId}/cancel`,
      {},
      {
        onSuccess: () => {
          alert("Order canceled successfully!");
          router.reload();
        },
        onError: () => {
          alert("Failed to cancel the order. Please try again.");
        },
      }
    );
  }
}

function deleteOrder(orderId) {
  if (confirm("Are you sure you want to delete this order?")) {
    router.delete(`/orders/${orderId}`, {
      onSuccess: () => {
        alert("Order deleted successfully!");
        router.reload();
      },
      onError: () => {
        alert("Failed to delete the order. Please try again.");
      },
    });
  }
}

// Check if order has expired (24+ hours)
function isOrderExpired(order) {
  if (!order.created_at) return true;
  const orderTime = dayjs(order.created_at);
  const now = dayjs();
  return now.diff(orderTime, "hour") >= 24;
}
</script>

<template>
  <AppLayout title="My Orders">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        My Orders
      </h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Header -->
          <h1 class="text-2xl font-bold mb-4">My Orders</h1>

          <!-- Orders Table -->
          <table class="w-full border-collapse border">
            <thead>
              <tr class="bg-gray-200">
                <th class="border px-4 py-2 text-left">Order ID</th>
                <th class="border px-4 py-2 text-left">Product</th>
                <th class="border px-4 py-2 text-left">Amount</th>
                <th class="border px-4 py-2 text-left">Status</th>
                <th class="border px-4 py-2 text-left">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="order in orders.data" :key="order.id" class="border">
                <td class="border px-4 py-2">{{ order.order_id }}</td>
                <td class="border px-4 py-2">
                  {{ order.product?.name || "N/A" }}
                </td>
                <td class="border px-4 py-2">₹{{ order.amount }}</td>
                <td class="border px-4 py-2">
                  <span
                    :class="{
                      'text-green-600': order.status === 'paid',
                      'text-red-600': order.status === 'cancelled',
                    }"
                  >
                    {{ order.status }}
                  </span>
                </td>
                <td class="border px-4 py-2">
                <!-- Cancel Order Button (Disabled after 24 hours) -->
                <button
                    v-if="order.status === 'pending' || order.status === 'paid'"
                    @click="cancelOrder(order.id)"
                    class="bg-red-500 text-white px-4 py-2 rounded-md"
                    :disabled="isOrderExpired(order)"
                    :class="{'opacity-50 cursor-not-allowed': isOrderExpired(order)}"
                >
                    Cancel Order
                </button>

                <!-- Delete Icon (Only for Canceled Orders) -->
                <TrashIcon
                    v-if="order.status === 'cancelled'"
                    @click="deleteOrder(order.id)"
                    class="w-6 h-6 text-red-500 hover:text-red-700 cursor-pointer ml-2"
                />
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination Controls -->
          <div class="mt-4 flex justify-between items-center">
            <span class="text-sm text-gray-700">
              Showing {{ orders.from }} to {{ orders.to }} of
              {{ orders.total }} entries
            </span>
            <div class="flex space-x-2">
              <button
                v-for="link in orders.links"
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

<script setup>
import { ref, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
  orderId: String,
  amount: Number,
  razorpayKey: String,
  user: Object,
  paymentMethod: String,
  transactionId: String,
});

const isProcessing = ref(false);
const errorMessage = ref("");
const isRazorpayLoaded = ref(false);

function loadRazorpayScript(callback) {
  if (window.Razorpay) {
    isRazorpayLoaded.value = true;
    if (callback) callback();
    return;
  }

  const script = document.createElement("script");
  script.src = "https://checkout.razorpay.com/v1/checkout.js";
  script.async = true;
  script.onload = () => {
    isRazorpayLoaded.value = true;
    if (callback) callback();
  };
  script.onerror = () => {
    errorMessage.value = "Failed to load Razorpay. Check your internet connection.";
  };

  document.body.appendChild(script);
}

//function which initiate payment logic
function initiatePayment() {
  if (!isRazorpayLoaded.value || !window.Razorpay) {
    errorMessage.value = "Razorpay SDK failed to load. Please try again.";
    return;
  }

  isProcessing.value = true;
  errorMessage.value = "";

  let options = {
    key: props.razorpayKey,
    amount: props.amount * 100,
    currency: "INR",
    name: "E-Commerce",
    description: "Product Purchase",
    order_id: props.orderId,
    prefill: {
      name: props.user.name,
      email: props.user.email,
    },
    handler: function (response) {
      router.post("/payment/verify", {
        razorpay_payment_id: response.razorpay_payment_id,
        razorpay_order_id: response.razorpay_order_id,
        razorpay_signature: response.razorpay_signature,
      }, {
        onSuccess: () => {
          alert("Payment Successful!");
          router.visit("/products");
        },
        onError: (err) => {
          isProcessing.value = false;
        }
      });
    },
    modal: {
      ondismiss: function () {
        console.log("Payment canceled");
        router.visit("/products");
      }
    },
    theme: { color: "#3399cc" }
  };

  let rzp = new Razorpay(options);
  rzp.on("payment.failed", function (response) {
    isProcessing.value = false;
  });

  rzp.open();
}

onMounted(() => {
  loadRazorpayScript();
});
</script>

<template>
  <AppLayout title="Checkout">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 py-12">
      <div class="bg-white shadow-2xl sm:rounded-xl p-8 text-center">
        <h1 class="text-3xl font-semibold mb-6 text-gray-800">Checkout</h1>
        
        <div class="space-y-4 mb-6 text-gray-700">
          <p class="text-lg">Amount: <span class="font-semibold text-xl text-green-600">₹{{ amount }}</span></p>
          <p class="text-md">Customer: <span class="font-medium">{{ user.name }}</span></p>
          <p class="text-md">Email: <span class="font-medium text-blue-600">{{ user.email }}</span></p>
        </div>
        <div class="mb-4">
          <button
            @click="initiatePayment"
            :disabled="!isRazorpayLoaded || isProcessing"
            class="px-4 py-2 text-base font-medium text-white bg-green-500 rounded-md shadow-sm hover:bg-green-600 focus:outline-none transition duration-300"
          >
            {{ isProcessing ? "Processing..." : "Pay Now" }}
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

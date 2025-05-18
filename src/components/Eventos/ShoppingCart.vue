<template>
  <div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="vintage-dark px-4 py-3">
      <h2 class="text-xl vintage-header text-white">
        <i class="fas fa-shopping-cart text-white mr-2"></i>
        <span class="text-white">Tu Carrito</span>
      </h2>
    </div>

    <div id="cartItems" class="p-4">
      <div v-if="cart.length === 0" class="text-gray-500 italic">
        Tu carrito está vacío
      </div>

      <div
        v-for="(item, index) in cart"
        :key="item.event.id"
        class="cart-item mb-3 pb-3 border-b border-dashed"
      >
        <div class="flex justify-between items-start">
          <div>
            <h4 class="font-medium">{{ item.event.title }}</h4>
            <p class="text-sm text-gray-600">
              €{{ item.event.price.toFixed(2) }} × {{ item.quantity }}
            </p>
          </div>
          <div class="text-right">
            <span class="font-medium">€{{ itemTotal(item) }}</span>
            <button @click="$emit('remove-item', index)" class="text-red-500 ml-2">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="cart.length > 0" id="cartSummary" class="p-4 border-t border-gray-200">
      <div class="flex justify-between mb-2">
        <span class="font-medium">Subtotal:</span>
        <span>€{{ subtotal.toFixed(2) }}</span>
      </div>
      <div class="flex justify-between mb-4">
        <span class="font-medium">Total:</span>
        <span class="font-bold">€{{ total.toFixed(2) }}</span>
      </div>

      <button @click="showCheckoutModal = true" class="vintage-button px-4 py-2 rounded-lg w-full">
        Proceder al pago
      </button>
    </div>
  </div>
  <CheckoutModal
    v-if="showCheckoutModal"
    :total="total"
    @close="showCheckoutModal = false"
  />
</template>

<script>
import CheckoutModal from './CheckoutModal.vue';

export default {
  props: {
    cart: Array,
  },
  emits: ['remove-item', 'checkout'],
  components: {
    CheckoutModal,
  },
  data() {
    return {
      showCheckoutModal: false,
    };
  },
  computed: {
    subtotal() {
      const cartArray = Array.isArray(this.cart) ? this.cart : [];
      return cartArray.reduce((sum, item) => sum + item.event.price * item.quantity, 0);
    },
    total() {
      return this.subtotal * 1.1; // 10% tax
    },
  },
  methods: {
    itemTotal(item) {
      return (item.event.price * item.quantity).toFixed(2);
    },
  },
};
</script>
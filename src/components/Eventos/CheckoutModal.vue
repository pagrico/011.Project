<template>
  <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md relative">
      <div class="vintage-dark px-6 py-4 rounded-t-lg flex items-center justify-between">
        <h3 class="text-xl vintage-header text-white" style="color: #fff;">Completa tu compra</h3>
        <button
          @click="$emit('close')"
          class="text-white hover:text-gray-300 text-2xl focus:outline-none ml-4"
          style="line-height: 1;"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="p-6">
        <form @submit.prevent="confirmPayment">
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Número de tarjeta</label>
            <input type="text" placeholder="1234 5678 9012 3456" class="form-input">
          </div>
          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de vencimiento</label>
              <input type="text" placeholder="MM/YY" class="form-input">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
              <input type="text" placeholder="123" class="form-input">
            </div>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre en la tarjeta</label>
            <input type="text" placeholder="Juan Pérez" class="form-input">
          </div>
          <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <span class="font-bold text-lg">Total: €{{ total.toFixed(2) }}</span>
            <button type="submit" class="vintage-button px-6 py-2 rounded-lg">
              Confirmar pago
            </button>
          </div>
        </form>
        <!-- Eliminado el botón "Cerrar" de la parte inferior -->
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    total: {
      type: Number,
      required: true
    }
  },
  methods: {
    confirmPayment() {
      console.log("Total recibido: €", this.total.toFixed(2));
      alert("¡Pago exitoso! Gracias por su compra.");
      this.$emit("close");
    },
  },
};
</script>
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
        <!-- MODAL DE FEEDBACK -->
        <div v-if="modalVisible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4 text-center">
            <div class="flex flex-col items-center mb-4">
              <span v-if="modalSuccess" class="text-green-600 text-5xl mb-2">
                <i class="fas fa-check-circle"></i>
              </span>
              <span v-else class="text-red-600 text-5xl mb-2">
                <i class="fas fa-times-circle"></i>
              </span>
              <div class="text-xl font-bold mb-2" :class="modalSuccess ? 'text-green-700' : 'text-red-700'">
                {{ modalTitle }}
              </div>
              <div class="text-gray-700">{{ modalMsg }}</div>
            </div>
            <button @click="closeModal" class="btn-secondary px-6 py-2 rounded-md font-medium mt-2">Cerrar</button>
          </div>
        </div>
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
  data() {
    return {
      modalVisible: false,
      modalMsg: '',
      modalSuccess: false,
      modalTitle: ''
    };
  },
  methods: {
    showModal(msg, success = false, title = '') {
      this.modalMsg = msg;
      this.modalSuccess = success;
      this.modalTitle = title || (success ? '¡Pago exitoso!' : 'Error');
      this.modalVisible = true;
    },
    closeModal() {
      this.modalVisible = false;
      if (this.modalSuccess) {
        this.$emit("close");
      }
    },
    confirmPayment() {
      // Aquí podrías validar los campos del formulario si lo deseas
      // Simula éxito siempre, pero podrías poner lógica real
      this.showModal("¡Pago exitoso! Gracias por su compra.", true, "¡Pago realizado!");
    },
  },
};
</script>

<style scoped>
.vintage-button {
  background-color: #825336;
  color: white;
  transition: all 0.3s ease;
}
.vintage-button:hover {
  background-color: #431605;
  transform: translateY(-2px);
}
.vintage-header {
  font-family: 'Playfair Display', serif;
  color: #1F1E1E;
}
.vintage-dark {
  background-color: #431605;
  color: white;
}
.form-input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #BCAAA1;
  border-radius: 0.375rem;
  transition: border-color 0.2s;
}
.form-input:focus {
  outline: none;
  border-color: #825336;
  box-shadow: 0 0 0 3px rgba(130, 83, 54, 0.1);
}
.btn-secondary {
  background-color: #B7CDDA;
  color: #431605;
  transition: all 0.3s ease;
}
.btn-secondary:hover {
  background-color: #BCAAA1;
}
</style>
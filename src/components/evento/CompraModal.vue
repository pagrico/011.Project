<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-[600px] p-8">
      <!-- Título del modal -->
      <h2 class="text-2xl font-bold mb-6">Comprar Entradas</h2>

      <!-- Detalles del evento -->
      <div class="mb-6">
        <p class="text-sm text-gray-600">Evento:</p>
        <p class="font-semibold text-lg">{{ evento.nombre }}</p>
        <p class="text-sm text-gray-500">{{ evento.lugar }}</p>
        <p class="text-sm text-gray-500">{{ evento.fecha.mes }} {{ evento.fecha.dia }} - {{ evento.hora }}</p>
      </div>

      <!-- Formulario de compra -->
      <div class="mb-6">
        <label for="cantidad" class="block text-sm text-gray-600 mb-2">Cantidad de entradas</label>
        <input
          id="cantidad"
          type="number"
          min="1"
          v-model="cantidad"
          class="w-full border border-gray-300 rounded px-4 py-2 text-sm"
        />
      </div>

      <!-- Botones -->
      <div class="flex justify-end space-x-4">
        <button @click="cerrarModal" class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded hover:bg-gray-100">
          Cancelar
        </button>
        <button @click="comprar" class="px-4 py-2 text-sm text-white bg-blue-500 rounded hover:bg-blue-600">
          Confirmar Compra
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';

defineProps({
  isOpen: {
    type: Boolean,
    required: true,
  },
  evento: {
    type: Object,
    required: true,
  },
});

defineEmits(['close', 'confirm']);

const cantidad = ref(1);

const cerrarModal = () => {
  emit('close'); // Emite el evento para cerrar el modal
};

const comprar = () => {
  emit('confirm', { evento, cantidad: cantidad.value });
  cerrarModal();
};
</script>

<style scoped>
/* Fondo con desenfoque para mantener la página visible detrás */
</style>

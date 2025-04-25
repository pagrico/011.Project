<template>
  <div class="flex items-center border-b border-gray-200 py-4">
    <!-- Fecha -->
    <div class="text-center mr-6">
      <span class="block text-sm text-gray-500">{{ evento.fecha.mes }}</span>
      <span class="block text-2xl font-bold">{{ evento.fecha.dia }}</span>
    </div>

    <!-- Detalles del evento -->
    <div class="flex-grow">
      <p class="text-sm text-gray-600">{{ evento.hora }}</p>
      <p class="font-semibold text-lg">{{ evento.nombre }}</p>
      <p class="text-sm text-gray-500">{{ evento.lugar }}</p>
    </div>

    <!-- Botón de entradas -->
    <button
      class="bg-blue-500 text-white px-4 py-2 rounded flex items-center hover:bg-blue-600"
      @click="$emit('abrir-modal', evento)"
    >
      Entradas
      <i class="icon-arrow-right ml-2"></i>
    </button>
  </div>
</template>

<script setup>
defineProps({
  evento: {
    type: Object,
    required: true,
  },
});
</script>

<style scoped>
.icon-arrow-right {
  /* Puedes usar un ícono de librería como FontAwesome o Heroicons */
}
</style>

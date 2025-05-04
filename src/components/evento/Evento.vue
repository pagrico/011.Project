<template>
  <div class="flex items-center border-b border-gray-200 py-4">
    <!-- Fecha -->
    <div class="text-center mr-6">
      <span class="block text-sm text-gray-500">{{ mesEnLetra }}</span>
      <span class="block text-2xl font-bold">{{ diaNumero }}</span>
    </div>

    <!-- Detalles del evento -->
    <div class="flex-grow">
      <p class="font-semibold text-lg">{{ props.evento.titulo }}</p>
      <p class="text-sm text-gray-500">{{ props.evento.descripcion }}</p>
      <p class="text-sm text-gray-600">Capacidad máxima: {{ props.evento.capacidad_maxima }}</p>
      <p class="text-sm text-gray-600">Visibilidad: {{ props.evento.visibilidad }}</p>
    </div>

    <!-- Botón de entradas -->
    <button
      class="bg-blue-500 text-white px-4 py-2 rounded flex items-center hover:bg-blue-600 mr-2"
      @click="$emit('abrir-modal', props.evento)"
    >
      Entradas
      <i class="icon-arrow-right ml-2"></i>
    </button>
    <!-- Botones solo para admin -->
    <template v-if="esAdmin">
      <button
        class="bg-yellow-500 text-white px-4 py-2 rounded flex items-center hover:bg-yellow-600 mr-2"
        @click="$emit('editar-evento', props.evento)"
      >
        Editar
        <i class="icon-edit ml-2"></i>
      </button>
      <button
        class="bg-red-500 text-white px-4 py-2 rounded flex items-center hover:bg-red-600"
        @click="$emit('eliminar-evento', props.evento)"
      >
        Eliminar
        <i class="icon-trash ml-2"></i>
      </button>
    </template>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  evento: {
    type: Object,
    required: true,
  },
});

// Formatear fecha: "2024-06-06" => 6 y "junio"
const diaNumero = computed(() => {
  if (!props.evento.fecha_evento) return '';
  const fecha = new Date(props.evento.fecha_evento);
  return fecha.getDate();
});

const mesEnLetra = computed(() => {
  if (!props.evento.fecha_evento) return '';
  const fecha = new Date(props.evento.fecha_evento);
  return fecha.toLocaleString('es-ES', { month: 'long' });
});

const esAdmin = computed(() => {
  try {
    const userData = JSON.parse(localStorage.getItem('userData'));
    return userData && String(userData.role) === '1';
  } catch {
    return false;
  }
});
</script>

<style scoped>
.icon-arrow-right {
  /* Puedes usar un ícono de librería como FontAwesome o Heroicons */
}
.icon-edit {
  /* Icono de editar */
}
.icon-trash {
  /* Icono de eliminar */
}
</style>

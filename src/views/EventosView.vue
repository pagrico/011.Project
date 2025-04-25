<template>
  <div class="p-6 bg-gray-100 text-gray-800">
    <!-- Título y filtro -->
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-5xl font-serif font-bold text-gray-800 tracking-wider">Entrevistas en Directo</h1>
      <div class="flex flex-col">
        <label for="filtro-ciudad" class="block text-sm font-light text-gray-700 mb-2">
          Selecciona una ciudad
        </label>
        <div class="relative">
          <select
            id="filtro-ciudad"
            v-model="ciudadSeleccionada"
            class="appearance-none border border-gray-300 rounded-md px-4 py-2 text-sm bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-400 focus:outline-none w-64 shadow-sm"
          >
            <option value="Todas">Todas</option>
            <option v-for="(ciudad, index) in eventosPorCiudad" :key="index" :value="ciudad.nombre">
              {{ ciudad.nombre }}
            </option>
          </select>
          <i class="absolute right-3 top-3 text-gray-400 pointer-events-none">
            ▼
          </i>
        </div>
      </div>
    </div>

    <!-- Listado de eventos -->
    <div>
      <div
        v-for="(ciudad, index) in eventosFiltrados"
        :key="index"
        class="mb-8"
      >
        <h2 class="text-3xl font-serif font-medium text-gray-800 border-b border-gray-300 pb-2 tracking-wide">
          {{ ciudad.nombre }}
        </h2>
        <Evento
          v-for="(evento, idx) in ciudad.eventos"
          :key="idx"
          :evento="evento"
          @abrir-modal="abrirModal"
        />
      </div>
    </div>

    <!-- Modal de compra -->
    <CompraModal
      v-if="modalAbierto"
      :isOpen="modalAbierto"
      :evento="eventoSeleccionado"
      @close="cerrarModal"
      @confirm="confirmarCompra"
    />
  </div>
</template>

<script setup>
import Evento from '../components/Evento.vue';
import CompraModal from '../components/CompraModal.vue';
import { ref, computed } from 'vue';

const eventosPorCiudad = [
  {
    nombre: 'Madrid',
    eventos: [
      {
        fecha: { mes: 'MAR', dia: 25 },
        hora: 'sáb - 20:30',
        nombre: 'Una Cena Cantada',
        lugar: 'Teatro Real',
      },
      {
        fecha: { mes: 'ABR', dia: 10 },
        hora: 'lun - 19:00',
        nombre: 'Hans Zimmer III Sinfónico',
        lugar: 'Auditorio Nacional',
      },
    ],
  },
  {
    nombre: 'Barcelona',
    eventos: [
      {
        fecha: { mes: 'MAY', dia: 5 },
        hora: 'vie - 21:00',
        nombre: 'Concierto de Primavera',
        lugar: 'Palau de la Música',
      },
    ],
  },
];

const totalEventos = eventosPorCiudad.reduce(
  (total, ciudad) => total + ciudad.eventos.length,
  0
);

const ciudadSeleccionada = ref('Todas');

const eventosFiltrados = computed(() => {
  if (ciudadSeleccionada.value === 'Todas') {
    return eventosPorCiudad;
  }
  return eventosPorCiudad.filter((ciudad) => ciudad.nombre === ciudadSeleccionada.value);
});

const modalAbierto = ref(false);
const eventoSeleccionado = ref(null);

const abrirModal = (evento) => {
  eventoSeleccionado.value = evento;
  modalAbierto.value = true;
};

const cerrarModal = () => {
  modalAbierto.value = false;
  eventoSeleccionado.value = null;
};

const confirmarCompra = (datos) => {
  console.log('Compra confirmada:', datos);
  cerrarModal();
};
</script>

<style scoped>
.icon-calendar {
  /* Puedes usar un ícono de librería como FontAwesome o Heroicons */
}
</style>
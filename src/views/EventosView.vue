<template>
  <div class="p-6 bg-gray-100 text-gray-800">
    <!-- Título y filtro -->
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-5xl font-serif font-bold text-gray-800 tracking-wider">Entrevistas en Directo</h1>
        <button
          v-if="esAdmin"
          class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-md shadow hover:bg-blue-700 transition"
          @click="abrirModalCrearEvento"
        >
          Añadir un nuevo evento
        </button>
      </div>
      <div class="flex flex-col">
        <label for="filtro-ciudad" class="block text-sm font-light text-gray-700 mb-2">
          Selecciona una ciudad
        </label>
        <div class="relative">
          <select
            id="filtro-ciudad"
            v-model="ciudadSeleccionada"
            class="appearance-none border border-gray-300 rounded-md px-4 py-2 text-sm bg-white hover:bg-gray-50 focus:ring-2 focus:ring-blue-400 focus:outline-none w-64 shadow-sm"
            :disabled="!!mensajeNoEventos || eventosPorCiudad.length === 0"
          >
            <option :value="'Todas'">Todas</option>
            <option v-for="(ciudad, index) in eventosPorCiudad" :key="index" :value="String(ciudad.nombre)">
              {{ ciudad.nombre }}
            </option>
          </select>
          <i class="absolute right-3 top-3 text-gray-400 pointer-events-none">
            ▼
          </i>
        </div>
      </div>
    </div>

    <!-- Listado de eventos o mensaje -->
    <div>
      <div v-if="mensajeNoEventos" class="text-xl text-center text-gray-500 py-16">
        {{ mensajeNoEventos }}
      </div>
      <div v-else>
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
            @editar-evento="abrirModalEditarEvento"
            @eliminar-evento="eliminarEvento"
          />
        </div>
      </div>
    </div>

    <!-- Modal de crear evento -->
    <CrearEventoModal
      v-if="modalCrearEventoAbierto"
      @close="cerrarModalCrearEvento"
      :evento-a-editar="eventoAEditar"
      :modo-edicion="modoEdicion"
      @evento-editado="onEventoEditado"
    />

    <!-- Modal de compra -->
    <CompraModal
      v-if="modalAbierto && eventoSeleccionado"
      :isOpen="modalAbierto"
      :evento="eventoSeleccionado"
      @close="cerrarModal"
      @confirm="confirmarCompra"
    />
  </div>
</template>

<script setup>
import Evento from '../components/evento/Evento.vue';
import CompraModal from '../components/evento/CompraModal.vue';
import CrearEventoModal from '../components/evento/CrearEventoModal.vue';
import { ref, computed, onMounted } from 'vue';

const eventosPorCiudad = ref([]);
const mensajeNoEventos = ref('');
const ciudadSeleccionada = ref('Todas');

const eventosFiltrados = computed(() => {
  if (String(ciudadSeleccionada.value) === 'Todas') {
    return eventosPorCiudad.value;
  }
  // Comparación robusta: ambos a string, trim y minúsculas
  const ciudad = eventosPorCiudad.value.find(
    (c) =>
      String(c.nombre).trim().toLowerCase() ===
      String(ciudadSeleccionada.value).trim().toLowerCase()
  );
  return ciudad ? [ciudad] : [];
});

const modalAbierto = ref(false);
const eventoSeleccionado = ref(null);

const abrirModal = (evento) => {
  eventoSeleccionado.value = evento;
  modalAbierto.value = true;
};

const cerrarModal = () => {
  eventoSeleccionado.value = null; // Limpiar referencia antes de cerrar el modal
  modalAbierto.value = false;
};

const confirmarCompra = (datos) => {
  console.log('Compra confirmada:', datos);
  cerrarModal();
};

const modalCrearEventoAbierto = ref(false);
const eventoAEditar = ref(null);
const modoEdicion = ref(false);

const abrirModalCrearEvento = () => {
  eventoAEditar.value = null;
  modoEdicion.value = false;
  modalCrearEventoAbierto.value = true;
};

const abrirModalEditarEvento = (evento) => {
  eventoAEditar.value = { ...evento };
  modoEdicion.value = true;
  modalCrearEventoAbierto.value = true;
};

const cerrarModalCrearEvento = () => {
  modalCrearEventoAbierto.value = false;
  eventoAEditar.value = null;
  modoEdicion.value = false;
};

const eliminarEvento = async (evento) => {
  if (confirm('¿Seguro que deseas eliminar este evento?')) {
    // Aquí deberías llamar a la API para eliminar el evento
    // await fetch('URL_ELIMINAR_EVENTO', { method: 'POST', body: ... })
    // Luego recargar la lista de eventos
    // Por ahora, solo lo quitamos del array local (ejemplo)
    eventosPorCiudad.value = eventosPorCiudad.value.map(ciudad => ({
      ...ciudad,
      eventos: ciudad.eventos.filter(e => e.id !== evento.id)
    }));
  }
};

const onEventoEditado = (eventoEditado) => {
  // Aquí puedes actualizar la lista de eventos localmente si lo deseas
  cerrarModalCrearEvento();
};

const onAddEvento = () => {
  abrirModalCrearEvento();
};

const esAdmin = computed(() => {
  try {
    const userData = JSON.parse(localStorage.getItem('userData'));
    return userData && String(userData.role) === '1';
  } catch {
    return false;
  }
});

// Cargar eventos desde la API al montar el componente
onMounted(async () => {
  try {
    const response = await fetch('http://localhost:8080/Apis/API_Lista_de_eventos.php');
    const data = await response.json();
    console.log(data);
    if (Array.isArray(data)) {
      if (data.length === 0) {
        mensajeNoEventos.value = 'Actualmente no hay eventos disponibles. Por favor, vuelva a consultar más adelante.';
        eventosPorCiudad.value = [];
      } else {
        eventosPorCiudad.value = data;
        mensajeNoEventos.value = '';
      }
    } else if (data.mensaje) {
      mensajeNoEventos.value = data.mensaje;
      eventosPorCiudad.value = [];
    }
  } catch (e) {
    mensajeNoEventos.value = 'Error al cargar los eventos.';
    eventosPorCiudad.value = [];
  }
});
</script>

<style scoped>
.icon-calendar {
  /* Puedes usar un ícono de librería como FontAwesome o Heroicons */
}
</style>
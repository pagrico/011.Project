<template>
  <div class="vintage-card p-6">
    <h2 class="text-xl font-serif mb-4 section-title">Dirección</h2>
    
    <div class="space-y-4" v-if="!loading">
      <!-- Campo para el código postal -->
      <div>
        <label class="block text-sm font-medium mb-1">Código Postal</label>
        <input type="text" class="vintage-input w-full" v-model="direccionLocal.cp">
      </div>
      <!-- Campo para la ciudad -->
      <div>
        <label class="block text-sm font-medium mb-1">Ciudad</label>
        <input type="text" class="vintage-input w-full" v-model="direccionLocal.ciudad">
      </div>
      <!-- Campo para la provincia -->
      <div>
        <label class="block text-sm font-medium mb-1">Provincia</label>
        <input type="text" class="vintage-input w-full" v-model="direccionLocal.provincia">
      </div>
      <!-- Campo para la calle -->
      <div>
        <label class="block text-sm font-medium mb-1">Calle</label>
        <input type="text" class="vintage-input w-full" v-model="direccionLocal.calle">
      </div>
      <!-- Campo para el número o puerta -->
      <div>
        <label class="block text-sm font-medium mb-1">Número/Puerta</label>
        <input type="text" class="vintage-input w-full" v-model="direccionLocal.numero" placeholder="Número/Puerta">
      </div>
      <!-- Campo para el piso o puerta -->
      <div>
        <label class="block text-sm font-medium mb-1">Piso / Puerta</label>
        <input type="text" class="vintage-input w-full" v-model="direccionLocal.piso">
      </div>
      <!-- Botón para guardar la dirección -->
      <button class="vintage-button mt-4 w-full" @click="guardarDireccion">
        <i class="fas fa-map-marker-alt mr-2"></i> Actualizar dirección
      </button>
    </div>
    <!-- Spinner de carga mientras se actualiza la dirección -->
    <div v-else class="flex justify-center items-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-darkbutton"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, inject } from 'vue';

// Inyectar la función showAlert desde el componente padre
const showAlert = inject('showAlert');

const props = defineProps({
  userId: {
    type: [Number, String],
    required: true
  },
  direccion: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['actualizar']);

const direccionLocal = ref({
  cp: '',
  ciudad: '',
  provincia: '',
  calle: '',
  numero: '',
  piso: ''
});

const loading = ref(false);

// Observar cambios en dirección
watch(() => props.direccion, (newDireccion) => {
  if (newDireccion) {
    direccionLocal.value = {
      cp: newDireccion.DIR_CP || '',
      ciudad: newDireccion.DIR_CIUDAD || '',
      provincia: newDireccion.DIR_PROVINCIA || '',
      calle: newDireccion.DIR_CALLE || '',
      piso: newDireccion.DIR_PISO || '',
      puerta: newDireccion.DIR_PUERTA || ''
    };
  }
}, { deep: true, immediate: true });

// Guardar dirección
const guardarDireccion = async () => {
  loading.value = true;
  const startTime = Date.now();
  
  try {
    const response = await fetch('http://localhost:8080/Apis/API_actualizar_direccion.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        userId: props.userId,
        cp: direccionLocal.value.cp,
        ciudad: direccionLocal.value.ciudad,
        provincia: direccionLocal.value.provincia,
        calle: direccionLocal.value.calle,
        piso: direccionLocal.value.piso,
        numero: direccionLocal.value.numero 
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showAlert('Dirección actualizada correctamente', true);
      emit('actualizar');
    } else {
      showAlert('Error al actualizar dirección: ' + data.error, false);
    }
  } catch (error) {
    console.error('Error al actualizar dirección:', error);
    showAlert('Error al conectar con el servidor', false);
  } finally {
    // Limitar tiempo de carga a máximo 2 segundos
    const elapsed = Date.now() - startTime;
    const maxTime = 2000; // 2 segundos máximo
    const delay = Math.max(0, Math.min(500, maxTime - elapsed));
    
    setTimeout(() => {
      loading.value = false;
    }, delay);
  }
};
</script>

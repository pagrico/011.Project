<template>
  <div class="vintage-card p-6">
     <h2 class="text-xl font-serif mb-4 section-title">Métodos de Pago</h2>
    
    <div class="space-y-4">

      <!-- Mensaje cuando no hay métodos de pago -->
      <div v-if="metodosPago.length === 0" class="text-center py-6 text-gray-500 italic">
        No tienes métodos de pago guardados.
      </div>
    
      <!-- Lista de métodos de pago -->
      <div v-for="metodo in metodosPago" :key="metodo.id" class="payment-card p-4 rounded-lg">
        <div class="flex justify-between items-start">
          <div>
            <!-- Tipo de método de pago -->
            <div class="text-xs uppercase text-darkbutton font-semibold mb-1">{{ metodo.tipo }}</div>
            <!-- Número de tarjeta enmascarado o titular según tipo -->
            <div class="text-sm mb-1">
              <span v-if="metodo.tipo === 'TARJETA'">•••• •••• •••• {{ metodo.numero?.substring(metodo.numero?.length - 4) }}</span>
              <span v-else-if="metodo.tipo === 'PAYPAL'">{{ metodo.titular }}</span>
              <span v-else>{{ metodo.titular }}</span>
            </div>
            <!-- Titular del método de pago -->
            <div class="text-xs text-gray-600">Titular: {{ metodo.titular }}</div>
            <!-- Fecha de expiración si existe -->
            <div v-if="metodo.fechaExp" class="text-xs text-gray-600">Expira: {{ formatExpirationDate(metodo.fechaExp) }}</div>
          </div>
          <div class="flex items-center space-x-2">
            <!-- Switch para activar/desactivar método de pago -->
            <label class="toggle-switch">
              <input type="checkbox" :checked="metodo.activo" @change="toggleMetodoPago(metodo.id, $event)">
              <span class="toggle-slider"></span>
            </label>
            <!-- Botón para editar método de pago -->
            <button class="text-darkbutton hover:text-button" @click="editarMetodoPago(metodo)">
              <i class="fas fa-edit"></i>
            </button>
            <!-- Botón para eliminar método de pago -->
            <button class="text-red-500 hover:text-red-700 cursor-pointer" @click="eliminarMetodoPago(metodo.id)">
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Botón para agregar nuevo método de pago -->
      <div class="border-2 border-dashed border-accent rounded-lg p-4 text-center hover:bg-gray-50 transition-colors cursor-pointer bg-white shadow-sm" @click="showAddMethodModal = true">
        <button class="text-darkbutton hover:text-button font-medium flex items-center justify-center w-full">
          <div class="bg-[#C18F67] text-white rounded-full w-6 h-6 flex items-center justify-center mr-2">
            <span>+</span>
          </div>
          <span class="text-[#8B5A2B] font-medium">Agregar nuevo método de pago</span>
        </button>
      </div>
    </div>
    
    <!-- Modal para agregar/editar método de pago -->
    <div class="modal-overlay" v-if="showAddMethodModal">
      <Teleport to="body">
        <AgregarMetodoPagoModal 
          :editar-metodo="metodoEditar"
          @close="cerrarModalMetodoPago" 
          @guardar="guardarMetodoPago" 
        />
      </Teleport>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, inject } from 'vue';
import AgregarMetodoPagoModal from './AgregarMetodoPagoModal.vue';

// Inyectar la función showAlert desde el componente padre
const showAlert = inject('showAlert');

const props = defineProps({
  userId: {
    type: [Number, String],
    required: true
  }
});

const metodosPago = ref([]);
const showAddMethodModal = ref(false);
const metodoEditar = ref(null);
const apiError = ref(''); // Para mostrar errores específicos de la API
const debugging = ref(process.env.NODE_ENV === 'development'); // Solo activar en modo desarrollo

// Observador para recargar cuando cambie el userId
watch(() => props.userId, (newVal, oldVal) => {
  if (newVal && newVal !== oldVal) {
    console.log('userId changed, reloading payment methods');
    cargarMetodosPago();
  }
}, { immediate: false });

// Cargar métodos de pago
const cargarMetodosPago = async () => {
  if (!props.userId) {
    console.error('Error: userId no proporcionado');
    apiError.value = 'No se proporcionó ID de usuario';
    return;
  }
  
  apiError.value = ''; // Limpiar errores anteriores
  
  try {
    console.log('Cargando métodos de pago para userId:', props.userId);
    const response = await fetch(`http://localhost:8080/Apis/API_metodos_pago.php?userId=${props.userId}`);
    const data = await response.json();
    
    if (data.success) {
      console.log('Métodos de pago recibidos:', data.metodosPago);
      metodosPago.value = data.metodosPago.map(metodo => ({
        id: metodo.MDP_PAGO,
        tipo: metodo.MDP_TIPO,
        numero: metodo.MDP_NUMERO || '',
        titular: metodo.MDP_TITULAR || '',
        fechaExp: metodo.MDP_FECHAEXP || null,
        activo: metodo.MDP_ACTIVO === "1" || metodo.MDP_ACTIVO === 1
      }));
    } else {
      console.error('Error al cargar métodos de pago:', data.error);
      apiError.value = data.error || 'Error desconocido al cargar datos';
    }
  } catch (error) {
    console.error('Error en la API:', error);
    apiError.value = error.message || 'Error al conectar con el servidor';
  }
};

// Formatear fecha de expiración
const formatExpirationDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return `${(date.getMonth() + 1).toString().padStart(2, '0')}/${date.getFullYear().toString().slice(-2)}`;
};

// Activar/desactivar método de pago
const toggleMetodoPago = async (id, event) => {
  const activo = event.target.checked;
  
  try {
    const response = await fetch('http://localhost:8080/Apis/API_toggle_metodo_pago.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        id: id,
        activo: activo ? 1 : 0
      })
    });
    
    const data = await response.json();
    
    if (!data.success) {
      console.error('Error al actualizar estado:', data);
      showAlert('Error al actualizar el estado del método de pago: ' + data.error, false);
      event.target.checked = !activo; // Revertir el cambio en la UI
    } else {
      // Recargar métodos de pago para asegurar que otros métodos se actualicen correctamente
      await cargarMetodosPago();
    }
  } catch (error) {
    console.error('Error al actualizar método de pago:', error);
    showAlert('Error al conectar con el servidor', false);
    event.target.checked = !activo; // Revertir el cambio en la UI
  }
};

// Editar método de pago
const editarMetodoPago = (metodo) => {
  metodoEditar.value = { ...metodo };
  showAddMethodModal.value = true;
};

// Eliminar método de pago
const eliminarMetodoPago = async (id) => {
  if (!confirm('¿Estás seguro de que deseas eliminar este método de pago?')) {
    return;
  }
  
  try {
    console.log('Eliminando método de pago con ID:', id);
    const response = await fetch('http://localhost:8080/Apis/API_eliminar_metodo_pago.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ 
        id: id,
        userId: props.userId
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Eliminar el método de pago de la lista local
      metodosPago.value = metodosPago.value.filter(metodo => metodo.id !== id);
      showAlert('Método de pago eliminado correctamente', true);
    } else {
      console.error('Error al eliminar método de pago:', data.error);
      showAlert('Error al eliminar método de pago: ' + data.error, false);
    }
  } catch (error) {
    console.error('Error al eliminar método de pago:', error);
    showAlert('Error al conectar con el servidor', false);
  }
};

// Cerrar modal de método de pago
const cerrarModalMetodoPago = () => {
  showAddMethodModal.value = false;
  metodoEditar.value = null;
};

// Guardar método de pago (nuevo o editado)
const guardarMetodoPago = async (metodo) => {
  try {
    const url = metodo.id 
      ? 'http://localhost:8080/Apis/API_actualizar_metodo_pago.php' 
      : 'http://localhost:8080/Apis/API_agregar_metodo_pago.php';
    
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        ...metodo,
        userId: props.userId
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      await cargarMetodosPago(); // Recargar los métodos de pago
      cerrarModalMetodoPago();
      showAlert(metodo.id ? 'Método de pago actualizado correctamente' : 'Método de pago agregado correctamente', true);
    } else {
      showAlert('Error al guardar método de pago: ' + data.error, false);
    }
  } catch (error) {
    console.error('Error al guardar método de pago:', error);
    showAlert('Error al conectar con el servidor', false);
  }
};

onMounted(() => {
  if (props.userId) {
    console.log('Componente montado con userId:', props.userId);
    cargarMetodosPago();
  } else {
    console.error('Error: componente montado sin userId');
    apiError.value = 'No se proporcionó ID de usuario';
  }
});
</script>

<style scoped>
.payment-card {
  background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(183,205,218,0.4) 100%);
  border: 1px solid rgba(188, 174, 161, 0.5);
  position: relative;
  overflow: hidden;
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.toggle-slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
  border-radius: 24px;
}

.toggle-slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .toggle-slider {
  background-color: #C18F67;
}

input:checked + .toggle-slider:before {
  transform: translateX(26px);
}

.icon-plus {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  margin-right: 8px;
  font-size: 16px;
  font-weight: bold;
  border-radius: 50%;
  background-color: #C18F67;
  color: white;
}

.border-accent {
  border-color: #C18F67;
  border-width: 2px;
}

/* Estilos adicionales para asegurar visibilidad */
.vintage-card {
  background-color: white;
  position: relative;
  z-index: 10;
}

/* Mejorar la visibilidad del modal */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}

/* Estilos para iconos de acciones */
button i {
  transition: transform 0.2s ease;
}

button:hover i.fa-trash-alt {
  transform: scale(1.2);
}

button:active i.fa-trash-alt {
  transform: scale(0.95);
}

/* Asegurar que todos los botones muestren el cursor de mano */
button {
  cursor: pointer;
}
</style>

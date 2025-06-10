<template>
  <div class="modal-backdrop">
    <div class="modal-content vintage-card p-6 w-full max-w-md mx-auto">
      <!-- Título del modal: cambia según si se edita o se agrega -->
      <h2 class="text-xl font-serif mb-4 section-title">
        {{ editarMetodo ? 'Editar método de pago' : 'Agregar método de pago' }}
      </h2>
      
      <div class="space-y-4 mt-6">
        <!-- Selector de tipo de método de pago (solo tarjeta habilitado) -->
        <div>
          <label class="block text-sm font-medium mb-1 text-darktext">Tipo</label>
          <select class="vintage-input w-full" v-model="metodo.tipo" :disabled="editarMetodo">
            <option value="TARJETA">Tarjeta</option>
            <option value="PAYPAL" disabled>PayPal (Próximamente)</option>
            <option value="TRANSFERENCIA" disabled>Transferencia bancaria (Próximamente)</option>
            <option value="OTRO" disabled>Otro (Próximamente)</option>
          </select>
          <p v-if="metodo.tipo !== 'TARJETA'" class="text-xs text-red-500 mt-1">
            Por el momento, solo se admite el método de pago con tarjeta.
          </p>
        </div>
        
        <!-- Campo para el número de tarjeta -->
        <div v-if="metodo.tipo === 'TARJETA'">
          <label class="block text-sm font-medium mb-1 text-darktext">Número de tarjeta</label>
          <input type="text" class="vintage-input w-full" v-model="metodo.numero" placeholder="1234 5678 9012 3456">
        </div>
        
        <!-- Campo para la fecha de expiración de la tarjeta -->
        <div v-if="metodo.tipo === 'TARJETA'">
          <label class="block text-sm font-medium mb-1 text-darktext">Fecha de expiración</label>
          <input type="month" class="vintage-input w-full" v-model="metodo.fechaExp">
        </div>
        
        <!-- Campo para el titular de la tarjeta -->
        <div>
          <label class="block text-sm font-medium mb-1 text-darktext">Titular</label>
          <input type="text" class="vintage-input w-full" v-model="metodo.titular" placeholder="Nombre completo">
        </div>
        
        <!-- Selector de estado (activo/inactivo) -->
        <div>
          <label class="block text-sm font-medium mb-1 text-darktext">Estado</label>
          <div class="flex items-center space-x-2">
            <label class="toggle-switch">
              <input type="checkbox" v-model="metodo.activo">
              <span class="toggle-slider"></span>
            </label>
            <span>{{ metodo.activo ? 'Activo' : 'Inactivo' }}</span>
          </div>
        </div>
        
        <!-- Botones de acción: guardar o cancelar -->
        <div class="flex space-x-3 mt-6">
          <button class="vintage-button w-1/2" @click="guardar">Guardar</button>
          <button class="vintage-button-secondary w-1/2" @click="$emit('close')">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
  editarMetodo: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['close', 'guardar']);

// Estado reactivo para el método de pago
const metodo = ref({
  id: null,
  tipo: 'TARJETA', // Siempre por defecto establecemos TARJETA
  numero: '',
  fechaExp: '',
  titular: '',
  activo: true
});

// Formatear fecha de expiración para el input month
const formatFechaExpParaInput = (fechaExp) => {
  const fecha = new Date(fechaExp);
  const year = fecha.getFullYear();
  const month = (fecha.getMonth() + 1).toString().padStart(2, '0');
  return `${year}-${month}`;
};

// Si estamos editando, cargar datos
watch(() => props.editarMetodo, (newMetodo) => {
  if (newMetodo) {
    metodo.value = {
      id: newMetodo.id,
      tipo: 'TARJETA', // Forzamos siempre a TARJETA independientemente del valor anterior
      numero: newMetodo.numero || '',
      fechaExp: newMetodo.fechaExp ? formatFechaExpParaInput(newMetodo.fechaExp) : '',
      titular: newMetodo.titular || '',
      activo: newMetodo.activo !== undefined ? newMetodo.activo : true
    };
  }
}, { immediate: true });

// Forzar tipo TARJETA al montar el componente
onMounted(() => {
  metodo.value.tipo = 'TARJETA';
});

// Guardar método de pago
const guardar = () => {
  // Validaciones básicas
  if (metodo.value.tipo === 'TARJETA' && !metodo.value.numero) {
    alert('Por favor, ingresa el número de tarjeta');
    return;
  }
  
  if (!metodo.value.titular) {
    alert('Por favor, ingresa el nombre del titular');
    return;
  }
  
  emit('guardar', {
    id: metodo.value.id,
    tipo: metodo.value.tipo,
    numero: metodo.value.numero,
    fechaExp: metodo.value.fechaExp,
    titular: metodo.value.titular,
    activo: metodo.value.activo
  });
};
</script>

<style scoped>
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

/* Nuevos estilos para el modal */
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(255, 255, 255, 0.9); /* Fondo blanco semi-transparente */
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1050;
}

.modal-content {
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); /* Sombra más suave */
  z-index: 1051;
  max-height: 90vh;
  overflow-y: auto;
  position: relative;
  width: 95%;
  max-width: 500px;
  border: 1px solid rgba(188, 174, 161, 0.3); /* Borde sutil */
}

.vintage-input {
  border-color: rgba(188, 174, 161, 0.6);
  background-color: rgba(255, 255, 255, 0.8);
  transition: all 0.3s ease;
  border-radius: 6px;
  padding: 8px 12px;
  margin-bottom: 8px;
}

.vintage-input:focus {
  border-color: #C18F67;
  box-shadow: 0 0 0 2px rgba(193, 143, 103, 0.2);
  outline: none;
}

.section-title {
  color: #8B5A2B;
  position: relative;
  padding-bottom: 8px;
}

.section-title::after {
  content: "";
  position: absolute;
  bottom: 0;
  left: 0;
  width: 60px;
  height: 3px;
  background: linear-gradient(90deg, #C18F67, transparent);
}
</style>

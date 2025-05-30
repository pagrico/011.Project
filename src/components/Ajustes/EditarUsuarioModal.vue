<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="vintage-card p-6 w-full max-w-lg mx-auto">
      <h2 class="text-xl font-serif mb-4 section-title">Editar Usuario</h2>
      
      <div class="space-y-4 mt-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" class="vintage-input w-full" v-model="usuarioLocal.nombre">
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">Apellidos</label>
            <input type="text" class="vintage-input w-full" v-model="usuarioLocal.apellidos">
          </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" class="vintage-input w-full" v-model="usuarioLocal.email">
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">Teléfono</label>
            <input type="tel" class="vintage-input w-full" v-model="usuarioLocal.telefono">
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1">Login</label>
          <input type="text" class="vintage-input w-full bg-gray-100" v-model="usuarioLocal.login" readonly>
        </div>
        
        <div>
          <label class="block text-sm font-medium mb-1">Rol</label>
          <select class="vintage-input w-full" v-model="usuarioLocal.rol">
            <option value="1">Administrador</option>
            <option value="2">Usuario</option>
          </select>
        </div>
        
        <div class="flex items-center justify-between">
          <label class="flex items-center">
            <input type="checkbox" class="form-checkbox h-5 w-5 text-darkbutton" v-model="resetPassword">
            <span class="ml-2 text-sm">Resetear contraseña</span>
          </label>
          
          <div v-if="resetPassword" class="w-1/2">
            <input type="text" class="vintage-input w-full" v-model="nuevaPassword" placeholder="Nueva contraseña">
          </div>
        </div>
        
        <div class="flex space-x-3 mt-6">
          <button class="vintage-button w-1/2" @click="guardar" :disabled="loading">
            <span v-if="loading">
              <i class="fas fa-circle-notch fa-spin mr-2"></i> Guardando...
            </span>
            <span v-else>
              <i class="fas fa-save mr-2"></i> Guardar cambios
            </span>
          </button>
          <button class="vintage-button-secondary w-1/2" @click="$emit('close')">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  usuario: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'guardar']);

const usuarioLocal = ref({ ...props.usuario });
const resetPassword = ref(false);
const nuevaPassword = ref('');
const loading = ref(false);

// Actualizar usuario local cuando cambia el prop
watch(() => props.usuario, (newUsuario) => {
  usuarioLocal.value = { ...newUsuario };
}, { deep: true });

// Guardar cambios
const guardar = () => {
  // Validación básica
  if (!usuarioLocal.value.nombre || !usuarioLocal.value.apellidos || !usuarioLocal.value.email) {
    alert('Por favor, completa los campos obligatorios: nombre, apellidos y email');
    return;
  }
  
  // Si se quiere resetear password, validar que haya una nueva
  if (resetPassword.value && !nuevaPassword.value) {
    alert('Por favor, ingresa una nueva contraseña');
    return;
  }
  
  // Validar email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(usuarioLocal.value.email)) {
    alert('Por favor, ingresa un email válido');
    return;
  }
  
  loading.value = true;
  
  // Crear objeto para enviar a la API
  const usuarioActualizado = {
    id: usuarioLocal.value.id,
    nombre: usuarioLocal.value.nombre,
    apellidos: usuarioLocal.value.apellidos,
    email: usuarioLocal.value.email,
    telefono: usuarioLocal.value.telefono,
    rol: usuarioLocal.value.rol,
    resetPassword: resetPassword.value,
    nuevaPassword: nuevaPassword.value
  };
  
  emit('guardar', usuarioActualizado);
  loading.value = false;
};
</script>

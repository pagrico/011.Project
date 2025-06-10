<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center">
    <div class="vintage-card p-6 w-full max-w-md mx-auto">
      <!-- Título del modal -->
      <h2 class="text-xl font-serif mb-4 section-title">Cambiar Contraseña</h2>
      
      <div class="space-y-4 mt-6">
        <!-- Campo para la contraseña actual -->
        <div>
          <label class="block text-sm font-medium mb-1">Contraseña actual</label>
          <input type="password" class="vintage-input w-full" v-model="passwordActual" placeholder="Ingresa tu contraseña actual">
        </div>
        <!-- Campo para la nueva contraseña -->
        <div>
          <label class="block text-sm font-medium mb-1">Nueva contraseña</label>
          <input type="password" class="vintage-input w-full" v-model="passwordNueva" placeholder="Ingresa tu nueva contraseña">
        </div>
        <!-- Campo para confirmar la nueva contraseña -->
        <div>
          <label class="block text-sm font-medium mb-1">Confirmar nueva contraseña</label>
          <input type="password" class="vintage-input w-full" v-model="passwordConfirmar" placeholder="Confirma tu nueva contraseña">
        </div>
        <!-- Botones de acción: cambiar o cancelar -->
        <div class="flex space-x-3 mt-6">
          <button class="vintage-button w-1/2" @click="cambiarPassword" :disabled="loading">
            <span v-if="loading">
              <i class="fas fa-circle-notch fa-spin mr-2"></i> Guardando...
            </span>
            <span v-else>
              <i class="fas fa-lock mr-2"></i> Cambiar contraseña
            </span>
          </button>
          <button class="vintage-button-secondary w-1/2" @click="$emit('close')">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, inject } from 'vue';

// Inyectar la función showAlert desde el componente padre
const showAlert = inject('showAlert');

const props = defineProps({
  userId: {
    type: [Number, String],
    required: true
  }
});

const emit = defineEmits(['close']);

const passwordActual = ref('');
const passwordNueva = ref('');
const passwordConfirmar = ref('');
const loading = ref(false);

// Cambiar contraseña
const cambiarPassword = async () => {
  // Validación
  if (!passwordActual.value) {
    showAlert('Por favor, ingresa tu contraseña actual', false);
    return;
  }
  
  if (!passwordNueva.value) {
    showAlert('Por favor, ingresa una nueva contraseña', false);
    return;
  }
  
  if (passwordNueva.value !== passwordConfirmar.value) {
    showAlert('Las contraseñas nuevas no coinciden', false);
    return;
  }
  
  // Validar longitud mínima
  if (passwordNueva.value.length < 6) {
    showAlert('La contraseña debe tener al menos 6 caracteres', false);
    return;
  }
  
  loading.value = true;
  
  try {
    const response = await fetch('http://localhost:8080/Apis/API_cambiar_password.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        userId: props.userId,
        passwordActual: passwordActual.value,
        passwordNueva: passwordNueva.value
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showAlert('Contraseña actualizada correctamente', true);
      emit('close');
    } else {
      showAlert('Error: ' + data.error, false);
    }
  } catch (error) {
    console.error('Error al cambiar la contraseña:', error);
    showAlert('Error al conectar con el servidor', false);
  } finally {
    loading.value = false;
  }
};
</script>

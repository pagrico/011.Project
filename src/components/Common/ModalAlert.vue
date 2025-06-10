<template>
  <Teleport to="body">
    <transition name="fade">
      <div v-if="isVisible" class="fixed inset-0 flex items-center justify-center z-[9999]">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="close"></div>
        <div class="bg-white rounded-lg p-6 shadow-lg max-w-md w-full mx-4 z-10 relative animate-appear">
          <!-- Icono de éxito o error -->
          <div class="flex justify-center mb-4">
            <div :class="[
              'rounded-full w-16 h-16 flex items-center justify-center', 
              isSuccess ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'
            ]">
              <i :class="[
                'text-2xl',
                isSuccess ? 'fas fa-check' : 'fas fa-exclamation'
              ]"></i>
            </div>
          </div>
          <!-- Título del modal según éxito o error -->
          <h3 class="text-center text-xl font-medium mb-2" :class="isSuccess ? 'text-green-700' : 'text-red-700'">
            {{ isSuccess ? '¡Éxito!' : '¡Error!' }}
          </h3>
          <!-- Mensaje del modal -->
          <p class="text-center text-gray-700 mb-6">{{ message }}</p>
          <!-- Botón para cerrar el modal -->
          <div class="flex justify-center">
            <button 
              @click="close" 
              :class="[
                'vintage-button px-10 py-2 rounded-md',
                isSuccess ? 'bg-[#C18F67]' : 'bg-red-500'
              ]">
              Cerrar
            </button>
          </div>
        </div>
      </div>
    </transition>
  </Teleport>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
  message: {
    type: String,
    default: ''
  },
  isSuccess: {
    type: Boolean,
    default: true
  },
  autoClose: {
    type: Boolean,
    default: false
  },
  duration: {
    type: Number,
    default: 3000
  }
});

const emit = defineEmits(['close']);
const isVisible = ref(true);

const close = () => {
  isVisible.value = false;
  emit('close');
};

onMounted(() => {
  if (props.autoClose) {
    setTimeout(() => {
      close();
    }, props.duration);
  }
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes appear {
  from {
    opacity: 0;
    transform: translateY(-20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-appear {
  animation: appear 0.3s ease-out;
}
</style>

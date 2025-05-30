<template>
  <div v-if="visible" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold text-[#431605]">Contacto sobre {{ serviceTitle }}</h3>
        <button @click="closeModal" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <form v-if="!success" @submit.prevent="submit">
        <div class="mb-4">
          <label class="block text-gray-700 mb-2 font-medium">Tu nombre</label>
          <input v-model="name" type="text" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-2 font-medium">Tu correo electrónico</label>
          <input v-model="email" type="email" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-2 font-medium">Tu mensaje</label>
          <textarea v-model="message" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]"></textarea>
        </div>
        <div v-if="error" class="text-red-600 mb-2">{{ error }}</div>
        <div class="flex justify-end">
          <button type="submit" class="btn-primary px-6 py-2 rounded-md font-medium">Enviar consulta</button>
        </div>
      </form>
      <div v-else class="text-center py-8">
        <div class="text-2xl text-[#431605] mb-4">¡Gracias por contactar!</div>
        <div class="text-gray-700 mb-4">
          En breves recibirás un correo de contestación.<br>
          Si no lo ves, revisa tu carpeta de spam.
        </div>
        <button @click="closeModal" class="btn-secondary px-6 py-2 rounded-md font-medium mt-2">Cerrar</button>
      </div>
    </div>
  </div>
</template>
<script setup>
import { ref, inject } from 'vue'

// Inyectar la función de alertas
const showAlert = inject('showAlert')

const props = defineProps(['visible', 'serviceTitle'])
const emit = defineEmits(['close'])
const name = ref('')
const email = ref('')
const message = ref('')
const error = ref('')
const success = ref(false)

function closeModal() {
  emit('close')
  name.value = ''
  email.value = ''
  message.value = ''
  error.value = ''
  success.value = false
}

async function submit() {
  error.value = ''
  if (!name.value.trim() || !email.value.trim() || !message.value.trim()) {
    showAlert('Por favor, completa todos los campos.', false)
    return
  }
  try {
    const res = await fetch('http://localhost:8080/Apis/API_contactar.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        nombre: name.value,
        email: email.value,
        mensaje: message.value,
        servicio: props.serviceTitle
      })
    })
    const data = await res.json()
    if (data.success) {
      showAlert('¡Gracias por contactar! En breve recibirás una respuesta.', true)
      success.value = true
    } else {
      showAlert('Error al enviar la consulta: ' + (data.error || ''), false)
    }
  } catch (e) {
    showAlert('Error de red al enviar el formulario.', false)
  }
}
</script>
<style scoped>
.btn-primary {
  background-color: #825336;
  color: white;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}
.btn-primary:hover {
  background-color: #431605;
}
.btn-secondary {
  background-color: #B7CDDA;
  color: #431605;
  transition: all 0.3s ease;
}
.btn-secondary:hover {
  background-color: #BCAA81;
}
</style>
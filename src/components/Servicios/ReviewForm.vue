<template>
  <div class="bg-[#DBE6ED] p-6 rounded-lg">
    <h4 class="font-bold text-[#431605] mb-4">Deja una reseña</h4>
    <form @submit.prevent="submit">
      <div class="mb-4">
        <label class="block text-gray-700 mb-2 font-medium">Tu valoración</label>
        <div class="star-rating flex">
          <i
            v-for="n in 5"
            :key="n"
            :class="['star', rating >= n ? 'fas' : 'far', 'fa-star', 'text-2xl', 'cursor-pointer', 'mr-1']"
            @click="rating = n"
          ></i>
        </div>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 mb-2 font-medium">Tu nombre</label>
        <input v-model="name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 mb-2 font-medium">Tu reseña</label>
        <textarea v-model="comment" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]"></textarea>
      </div>
      <button type="submit" class="btn-secondary w-full py-3 rounded-md font-medium">Enviar reseña</button>
    </form>
    <div v-if="successMsg" class="text-green-700 mt-4">{{ successMsg }}</div>
    <div v-if="errorMsg" class="text-red-700 mt-4">{{ errorMsg }}</div>
  </div>
</template>
<script setup>
import { ref } from 'vue'
const props = defineProps(['serviceId'])
const emit = defineEmits(['review-added'])
// Obtener nombre y apellidos del localStorage si existen
let defaultName = ''
try {
  const userData = JSON.parse(localStorage.getItem('userData'))
  if (userData && userData.name && userData.apellidos) {
    defaultName = `${userData.name} ${userData.apellidos}`
  }
} catch (e) {
  defaultName = ''
}
const rating = ref(0)
const name = ref(defaultName)
const comment = ref('')
const successMsg = ref('')
const errorMsg = ref('')

async function submit() {
  successMsg.value = ''
  errorMsg.value = ''
  if (!name.value.trim() || !comment.value.trim() || rating.value < 1) {
    errorMsg.value = 'Por favor, completa todos los campos y selecciona una valoración.'
    return
  }
  try {
    const res = await fetch('http://localhost:8080/Apis/API_crear_valoracion.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        nombre: name.value,
        valoracion: comment.value,
        estrellas: rating.value,
        servicio: props.serviceId
      })
    })
    const data = await res.json()
    if (data.success) {
      successMsg.value = '¡Gracias por tu reseña!'
      rating.value = 0
      name.value = defaultName
      comment.value = ''
      emit('review-added') // Notifica al padre para refrescar
    } else {
      errorMsg.value = 'Error al enviar la reseña: ' + (data.error || '')
    }
  } catch (e) {
    errorMsg.value = 'Error de red al enviar la reseña'
  }
}
</script>
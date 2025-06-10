<template>
  <!-- Contenedor principal del formulario -->
  <div class="mb-8">
    <!-- Título del formulario -->
    <h4 class="font-bold text-[#431605] mb-4">Editar servicio</h4>

    <!-- Formulario con evento submit que evita recarga y ejecuta 'save' -->
    <form @submit.prevent="save">

      <!-- Campo: Título -->
      <div class="mb-4">
        <label class="block text-gray-700 mb-2 font-medium">Título</label>
        <input v-model="form.title" type="text"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
      </div>

      <!-- Campo: Descripción -->
      <div class="mb-4">
        <label class="block text-gray-700 mb-2 font-medium">Descripción</label>
        <textarea v-model="form.description" rows="3"
          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]"></textarea>
      </div>

      <!-- Campos: Precio y Categoría organizados en dos columnas -->
      <div class="grid grid-cols-2 gap-4 mb-4">
        <!-- Precio -->
        <div>
          <label class="block text-gray-700 mb-2 font-medium">Precio (€)</label>
          <input v-model.number="form.price" type="number"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
        </div>

        <!-- Categoría -->
        <div>
          <label class="block text-gray-700 mb-2 font-medium">Categoría</label>
          <select v-model="form.category"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
            <option value="1">Producción audiovisual</option>
            <option value="2">Redes sociales</option>
            <option value="3">Podcasting</option>
            <option value="4">Consultoría</option>
          </select>
        </div>
      </div>

      <!-- Campo: Imágenes del servicio -->
      <div class="mb-4">
        <label class="block text-gray-700 mb-2 font-medium">Imágenes del servicio</label>

        <!-- Input + botón para añadir nueva imagen por URL -->
        <div class="flex mb-2">
          <input v-model="nuevaImagen" type="url" placeholder="Pega la URL de la imagen y pulsa Añadir"
            class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
          <button type="button" @click="agregarImagen"
            class="ml-2 px-4 py-2 bg-[#BCAA81] text-white rounded-md">Añadir</button>
        </div>

        <!-- Miniaturas de las imágenes cargadas con botón para eliminarlas -->
        <div v-if="imagenes.length" class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-2">
          <div v-for="(img, idx) in imagenes" :key="idx" class="relative group">
            <img :src="img" alt="Imagen subida"
              class="w-full h-24 object-cover rounded border border-[#BCAA81]">
            <button type="button" @click="eliminarImagen(idx)"
              class="absolute top-1 right-1 bg-white bg-opacity-80 rounded-full p-1 text-red-600 hover:text-red-800 opacity-80 group-hover:opacity-100 transition">
              <i class="fas fa-times"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Botones: disponibilidad y acciones -->
      <div class="flex items-center justify-between">
        <!-- Checkbox para marcar disponibilidad -->
        <div class="flex items-center">
          <input v-model="form.available" type="checkbox" class="mr-2">
          <label class="text-gray-700">Disponible</label>
        </div>

        <!-- Botones de acción: Actualizar y Cancelar -->
        <div class="flex space-x-2">
          <button type="submit" class="btn-primary px-4 py-2 rounded-md font-medium">Actualizar</button>
          <button type="button" @click="$emit('cancel')" class="btn-secondary px-4 py-2 rounded-md font-medium">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</template>
<script setup>
// Importación de utilidades reactivas de Vue
import { reactive, watch, ref } from 'vue'

// Props entrantes: servicio a editar
const props = defineProps(['service'])

// Eventos emitidos: 'saved' (cuando se guarda), 'cancel' (cuando se cancela)
const emit = defineEmits(['saved', 'cancel'])

// Estado reactivo que representa el formulario
const form = reactive({ ...props.service })

// Estado para las imágenes: array con URLs
const imagenes = ref([])

// Campo controlado para nueva imagen que se desea añadir
const nuevaImagen = ref('')

// Watcher: actualiza el formulario cuando cambian las props
watch(() => props.service, (val) => {
  // Actualiza campos del formulario
  Object.assign(form, val)

  // Detecta el formato del campo imágenes, soporta tanto arrays como strings JSON
  if (Array.isArray(val.imagenes)) {
    imagenes.value = [...val.imagenes]
  } else if (typeof val.imagenes === 'string' && val.imagenes.trim().startsWith('[')) {
    try {
      imagenes.value = JSON.parse(val.imagenes)
    } catch {
      imagenes.value = []
    }
  } else if (val.SER_IMAGENES && typeof val.SER_IMAGENES === 'string' && val.SER_IMAGENES.trim().startsWith('[')) {
    try {
      imagenes.value = JSON.parse(val.SER_IMAGENES)
    } catch {
      imagenes.value = []
    }
  } else {
    imagenes.value = []
  }
})

// Añadir imagen: sólo si hay URL válida y menos de 12 imágenes
function agregarImagen() {
  if (nuevaImagen.value.trim() && imagenes.value.length < 12) {
    imagenes.value.push(nuevaImagen.value.trim())
    nuevaImagen.value = ''
  }
}

// Eliminar imagen por índice
function eliminarImagen(idx) {
  imagenes.value.splice(idx, 1)
}

// Guardar cambios: emite los datos del formulario con imágenes actualizadas
function save() {
  emit('saved', { ...form, imagenes: imagenes.value })
}
</script>

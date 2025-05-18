<template>
  <div class="mb-4">
    <label class="block text-gray-700 mb-2 font-medium">Imágenes del servicio</label>
    <GestorArchuivos @change="onGestorArchivos" :images="imagenes" />
    
    
  </div>
</template>

<script setup>
import { computed } from 'vue'
import GestorArchuivos from './GestorArchuivos.vue'
const props = defineProps({
  imagenes: Array
})
const emit = defineEmits(['update:imagenes', 'eliminarImagen', 'handleGestorArchivos'])

const imagenes = computed({
  get: () => props.imagenes,
  set: v => emit('update:imagenes', v)
})

function eliminarImagen(idx) {
  emit('eliminarImagen', idx)
}
function onGestorArchivos(files) {
  emit('handleGestorArchivos', files)
}
</script>

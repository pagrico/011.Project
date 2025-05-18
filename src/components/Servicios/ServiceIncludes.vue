<template>
  <div class="mb-4">
    <label class="block text-gray-700 mb-2 font-medium">Incluye</label>
    <div class="flex mb-2">
      <input v-model="nuevoIncluye" type="text" placeholder="Añadir elemento" class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
      <button type="button" @click="agregarIncluye" class="ml-2 px-4 py-2 bg-[#BCAA81] text-white rounded-md">Añadir</button>
    </div>
    <ul class="pl-2">
      <li v-for="(item, idx) in incluye" :key="idx" class="flex items-center justify-between mb-1">
        <span class="flex items-center">
          <span class="inline-block w-2 h-2 rounded-full bg-[#BCAA81] mr-2"></span>
          {{ item }}
        </span>
        <button type="button" @click="eliminarIncluye(idx)" class="ml-2 text-red-500 hover:text-red-700">Quitar</button>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
const props = defineProps({
  incluye: Array
})
const emit = defineEmits(['update:incluye'])

const nuevoIncluye = ref('')

const incluye = computed({
  get: () => props.incluye,
  set: v => emit('update:incluye', v)
})

function agregarIncluye() {
  if (nuevoIncluye.value.trim() !== '') {
    incluye.value.push(nuevoIncluye.value.trim())
    emit('update:incluye', [...incluye.value])
    nuevoIncluye.value = ''
  }
}
function eliminarIncluye(idx) {
  incluye.value.splice(idx, 1)
  emit('update:incluye', [...incluye.value])
}
</script>

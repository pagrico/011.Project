<template>
  <div>
    <div class="mb-4">
      <label for="serviceTitle" class="block text-gray-700 mb-2 font-medium">Título</label>
      <input v-model="title" type="text" id="serviceTitle" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
    </div>
    <div class="mb-4">
      <label for="serviceShortDesc" class="block text-gray-700 mb-2 font-medium">Descripción corta</label>
      <textarea v-model="descripcion_corta" id="serviceShortDesc" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]"></textarea>
    </div>
    <div class="mb-4">
      <label for="serviceLongDesc" class="block text-gray-700 mb-2 font-medium">Descripción larga</label>
      <textarea v-model="descripcion_larga" id="serviceLongDesc" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]"></textarea>
    </div>
    <div class="mb-4">
      <label class="block text-gray-700 mb-2 font-medium">Estado</label>
      <div class="flex items-center">
        <label class="relative inline-flex items-center cursor-pointer">
          <input type="checkbox" v-model="estadoActivo" class="sr-only peer">
          <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#BCAA81] rounded-full peer peer-checked:bg-[#BCAA81] transition-all"></div>
          <span class="ml-3 text-sm font-medium text-gray-900">{{ estadoActivo ? 'Activo' : 'Oculto' }}</span>
        </label>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label for="servicePrice" class="block text-gray-700 mb-2 font-medium">Precio (€)</label>
        <input v-model.number="price" type="number" id="servicePrice" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
      </div>
    </div>
    <div class="mb-4">
      <label for="serviceIcon" class="block text-gray-700 mb-2 font-medium">Icono</label>
      <div class="flex items-center space-x-3">
        <select v-model="icon" id="serviceIcon" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
          <option v-for="option in iconOptions" :key="option.value" :value="option.value">
            {{ option.label }}
          </option>
        </select>
        <span v-if="icon">
          <i :class="['fas', icon, 'text-2xl', 'text-[#BCAA81]']"></i>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
const props = defineProps({
  title: String,
  descripcion_corta: String,
  descripcion_larga: String,
  price: Number,
  icon: String,
  estadoActivo: Boolean,
  iconOptions: Array
})
const emit = defineEmits(['update:title', 'update:descripcion_corta', 'update:descripcion_larga', 'update:price', 'update:icon', 'update:estadoActivo'])

const title = computed({
  get: () => props.title,
  set: v => emit('update:title', v)
})
const descripcion_corta = computed({
  get: () => props.descripcion_corta,
  set: v => emit('update:descripcion_corta', v)
})
const descripcion_larga = computed({
  get: () => props.descripcion_larga,
  set: v => emit('update:descripcion_larga', v)
})
const price = computed({
  get: () => props.price,
  set: v => emit('update:price', v)
})
const icon = computed({
  get: () => props.icon,
  set: v => emit('update:icon', v)
})
const estadoActivo = computed({
  get: () => props.estadoActivo,
  set: v => emit('update:estadoActivo', v)
})
const iconOptions = props.iconOptions
</script>

<template>
  <div class="mb-8">
    <h4 class="font-bold text-[#431605] mb-4">Editar servicio</h4>
    <form @submit.prevent="save">
      <div class="mb-4">
        <label class="block text-gray-700 mb-2 font-medium">Título</label>
        <input v-model="form.title" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 mb-2 font-medium">Descripción</label>
        <textarea v-model="form.description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]"></textarea>
      </div>
      <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
          <label class="block text-gray-700 mb-2 font-medium">Precio (€)</label>
          <input v-model.number="form.price" type="number" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
        </div>
        <div>
          <label class="block text-gray-700 mb-2 font-medium">Categoría</label>
          <select v-model="form.category" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
            <option value="1">Producción audiovisual</option>
            <option value="2">Redes sociales</option>
            <option value="3">Podcasting</option>
            <option value="4">Consultoría</option>
          </select>
        </div>
      </div>
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          <input v-model="form.available" type="checkbox" class="mr-2">
          <label class="text-gray-700">Disponible</label>
        </div>
        <div class="flex space-x-2">
          <button type="submit" class="btn-primary px-4 py-2 rounded-md font-medium">Actualizar</button>
          <button type="button" @click="$emit('cancel')" class="btn-secondary px-4 py-2 rounded-md font-medium">Cancelar</button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'
const props = defineProps(['service'])
const emit = defineEmits(['saved', 'cancel'])
const form = reactive({ ...props.service })
watch(() => props.service, (val) => {
  Object.assign(form, val)
})
function save() {
  // Lógica para actualizar servicio...
  emit('saved', form)
}
</script>
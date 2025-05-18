<template>
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <CreateServiceForm
      :serviceToEdit="editingService"
      @created="handleCreatedOrUpdated"
      @updated="handleCreatedOrUpdated"
    />
    <ServiceManager
      :services="services"
      :loading="loading"
      @edit="setEditingService"
      @update="emitUpdate"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import CreateServiceForm from './CreateServices.vue'
import ServiceManager from './ServicesManager.vue'
const props = defineProps(['services', 'loading'])
const emit = defineEmits(['update'])

const editingService = ref(null)

function setEditingService(service) {
  editingService.value = service
}
function handleCreatedOrUpdated() {
  editingService.value = null
  emit('update')
}
function emitUpdate() {
  emit('update')
}
</script>
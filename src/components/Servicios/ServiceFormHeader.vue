<template>
  <div class="admin-card-header px-6 py-4">
    <h3 class="text-lg font-bold">{{ isEditMode ? 'Editar servicio' : 'Crear nuevo servicio' }}</h3>
  </div>
</template>

<script setup>
defineProps({
  isEditMode: Boolean
})
</script>

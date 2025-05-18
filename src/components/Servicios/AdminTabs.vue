<template>
  <div class="flex border-b border-gray-200 mb-8">
    <button
      :class="['admin-tab px-4 py-2 font-medium', activeTab === 'servicios' ? 'active' : '']"
      @click="$emit('tab-change', 'servicios')"
    >Servicios</button>
    <button
      :class="['admin-tab px-4 py-2 font-medium', activeTab === 'requests' ? 'active' : '']"
      @click="$emit('tab-change', 'requests')"
    >Solicitudes</button>
    <button
      :class="['admin-tab px-4 py-2 font-medium', activeTab === 'all-requests' ? 'active' : '']"
      @click="$emit('tab-change', 'all-requests')"
    >Todas las solicitudes</button>
  </div>
</template>

<script setup>
const props = defineProps(['activeTab'])
</script>
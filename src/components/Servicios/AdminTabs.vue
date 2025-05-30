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

<style scoped>
.admin-tab {
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  border-bottom: 3px solid transparent;
  transition: all 0.3s ease;
  border-bottom: 3px solid transparent;
}
.admin-tab:hover, .admin-tab.active {
  border-bottom-color: #825336;
  color: #431605;
  font-weight: 500;
}
</style>
<template>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="stat-card p-4">
      <div class="stat-value">{{ activeServices }}</div>
      <div class="stat-label">Servicios activos</div>
    </div>
    <div class="stat-card p-4">
      <div class="stat-value">{{ pendingRequests }}</div>
      <div class="stat-label">Solicitudes pendientes</div>
    </div>
    <div class="stat-card p-4">
      <div class="stat-value">{{ averageRating }}</div>
      <div class="stat-label">Valoración media</div>
    </div>
    <div class="stat-card p-4">
      <div class="stat-value">{{ monthRevenue }}</div>
      <div class="stat-label">Este mes</div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps([
  'activeServices',
  'pendingRequests',
  'averageRating',
  'monthRevenue'
])
</script>
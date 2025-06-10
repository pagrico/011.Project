<template>
  <!-- Tarjetas de estadísticas rápidas del panel de administración de servicios -->
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

<style scoped>
.stat-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}
.stat-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
}
.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #431605;
}
.stat-label {
  color: #825336;
  font-size: 0.9rem;
}
</style>
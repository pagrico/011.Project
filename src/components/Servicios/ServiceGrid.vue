<template>
  <!-- Cuadrícula de servicios activos -->
  <section class="mb-20">
    <h2 class="text-2xl font-semibold mb-8 text-[#431605] border-b border-[#BCAA81] pb-2">
      Nuestros servicios
    </h2>
    <div v-if="loading" class="text-center py-10 text-[#431605]">Cargando servicios...</div>
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <ServiceCard
        v-for="service in filteredServices"
        :key="service.id"
        :service="service"
        class="animate-fade-in cursor-pointer"
        @click="emit('learnMore', service.id)"
      />
    </div>
  </section>
</template>
<script setup>
import ServiceCard from './ServiceCard.vue'
import { defineEmits, defineProps, computed } from 'vue'

const emit = defineEmits(['learnMore'])
const { services = [], loading = false } = defineProps(['services', 'loading'])

// Solo servicios activos (protección extra)
const filteredServices = computed(() => services.filter(s => s.status === 'Activo'))
</script>
<style scoped>
.service-grid-card {
  transition: all 0.3s ease;
  background: linear-gradient(135deg, white 0%, #DBE6ED 100%);
  border: 1px solid #B7CDDA;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  position: relative;
  overflow: hidden;
}
.service-grid-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
.service-grid-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 5px;
  background: linear-gradient(90deg, #BCAA81, #825336);
}
.service-grid-card:hover::before {
  height: 8px;
}
.price-tag {
  background-color: #BCAA81;
  color: #431605;
  position: absolute;
  top: 15px;
  right: 15px;
  transform: rotate(5deg);
}
.btn-primary {
  background-color: #825336;
  color: white;
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}
.btn-primary:hover {
  background-color: #431605;
}
.btn-primary::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 5px;
  height: 5px;
  background: rgba(255, 255, 255, 0.5);
  opacity: 0;
  border-radius: 100%;
  transform: scale(1, 1) translate(-50%, -50%);
  transform-origin: 50% 50%;
}
.btn-primary:hover::after {
  animation: ripple 1s ease-out;
}
@keyframes ripple {
  0% {
    transform: scale(0, 0);
    opacity: 0.5;
  }
  100% {
    transform: scale(20, 20);
    opacity: 0;
  }
}
.service-icon {
  font-size: 2.5rem;
  color: #825336;
  margin-bottom: 1rem;
}
</style>
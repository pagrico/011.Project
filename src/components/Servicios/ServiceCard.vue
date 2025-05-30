<template>
  <div class="service-grid-card rounded-lg overflow-hidden p-8 relative">
    <div class="service-icon text-center mb-4">
      <!-- Solo icono, sin imágenes -->
      <i :class="['fas', service.icon]"></i>
    </div>
    <h3 class="text-xl font-bold text-[#431605] mb-4 text-center">{{ service.title }}</h3>
    <p class="text-gray-700 mb-6 text-center">{{ service.desc }}</p>
    <span class="price-tag px-4 py-2 rounded-full text-sm font-bold absolute top-4 right-4 rotate-6">{{ service.price }}</span>
    <div class="text-center mt-10">
      <button @click="$emit('learnMore', service.id)" class="btn-primary px-8 py-3 rounded-md font-medium">Más información</button>
    </div>
  </div>
</template>
<script setup>
defineProps(['service'])
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
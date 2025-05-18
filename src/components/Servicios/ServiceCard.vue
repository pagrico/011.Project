<template>
  <div class="service-grid-card rounded-lg overflow-hidden p-8 relative">
    <div class="service-icon text-center">
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
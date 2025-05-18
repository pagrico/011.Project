<template>
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
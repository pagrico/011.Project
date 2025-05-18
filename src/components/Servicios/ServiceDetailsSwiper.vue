<template>
  <section class="mb-20">
    <h2 class="text-2xl font-semibold mb-8 text-[#431605] border-b border-[#BCAA81] pb-2">
      Detalles del servicio
    </h2>
    <div v-if="loading" class="text-center py-10 text-[#431605]">Cargando detalles...</div>
    <Swiper
      v-else
      class="service-swiper"
      :slides-per-view="1"
      :space-between="30"
      :pagination="{ clickable: true }"
      :navigation="false"
      ref="swiperRef"
      autoHeight
      @swiper="onSwiperReady"
    >
      <SwiperSlide v-for="service in filteredServices" :key="service.id">
        <ServiceDetailCard :service="service" @refresh="emitRefresh" />
      </SwiperSlide>
    </Swiper>
  </section>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'
import 'swiper/css/navigation'
import 'swiper/css/pagination'
import ServiceDetailCard from './ServiceDetailCard.vue'

const swiperRef = ref(null)
const swiperInstance = ref(null)
const emit = defineEmits(['refresh'])

const { services = [], loading = false } = defineProps(['services', 'loading'])

// Solo servicios activos (protección extra)
const filteredServices = computed(() => services.filter(s => s.status === 'Activo'))

// Guardar la instancia real del swiper cuando esté lista
function onSwiperReady(swiper) {
  swiperInstance.value = swiper
}

// Expose a method for parent to scroll to the correct slide
function scrollToService(serviceId) {
  const idx = services.findIndex(s => s.id === serviceId)
  if (idx >= 0) {
    const maxRetries = 20
    const delay = 150
    const trySlide = (retries = maxRetries) => {
      if (swiperInstance.value) {
        swiperInstance.value.slideTo(idx)
        setTimeout(() => {
          document.querySelector('.service-swiper').scrollIntoView({ behavior: 'smooth' })
        }, 200)
      } else if (retries > 0) {
        setTimeout(() => trySlide(retries - 1), delay)
      } else {
        alert('No se pudo mostrar el detalle del servicio. Intenta de nuevo.')
        console.warn('Swiper no está listo para moverse al slide')
      }
    }
    trySlide()
  }
}

function emitRefresh() {
  emit('refresh')
}

defineExpose({ scrollToService })
</script>
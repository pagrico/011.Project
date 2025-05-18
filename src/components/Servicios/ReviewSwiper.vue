<template>
  <div>
    <div v-if="!reviews || reviews.length === 0" class="text-center text-[#431605] mb-8">
      Aún no hay reseñas para este servicio. ¡Sé el primero en compartir tu experiencia y ayudar a otros usuarios!
    </div>
    <div v-else class="relative group">
      <Swiper
        class="review-swiper mb-8"
        :slides-per-view="1"
        :navigation="false"
        autoHeight
        ref="swiperRef"
        @swiper="onSwiperReady"
        @slideChange="onSlideChange"
      >
        <SwiperSlide v-for="review in reviews" :key="review.author">
          <div class="review-card p-6 mb-4 rounded-lg">
            <div class="flex items-center mb-4">
              <div class="star-rating flex mr-3">
                <i v-for="n in 5" :key="n"
                  :class="['fa-star', n <= review.rating ? 'fas star' : 'fas star empty']"></i>
              </div>
              <span class="text-sm font-medium text-gray-600">{{ review.author }}</span>
            </div>
            <p class="text-gray-700 pl-2">"{{ review.text }}"</p>
          </div>
        </SwiperSlide>
      </Swiper>
      <!-- Botón izquierdo: solo si no es el primero -->
      <button
        v-if="reviews.length > 1 && currentIndex > 0 && currentIndex < reviews.length"
        class="swiper-custom-btn swiper-custom-btn-prev"
        @click="slidePrev"
        aria-label="Anterior"
      >
        <i class="fas fa-chevron-left"></i>
      </button>
      <!-- Botón derecho: solo si no es el último -->
      <button
        v-if="reviews.length > 1 && currentIndex < reviews.length - 1"
        class="swiper-custom-btn swiper-custom-btn-next"
        @click="slideNext"
        aria-label="Siguiente"
      >
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>
  </div>
</template>
<script setup>
import { ref, watch } from 'vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'
import 'swiper/css/navigation'
const props = defineProps(['reviews'])

const swiperRef = ref(null)
const swiperInstance = ref(null)
const currentIndex = ref(0)

function onSwiperReady(swiper) {
  swiperInstance.value = swiper
  currentIndex.value = swiper.activeIndex || 0
}
function slideNext() {
  swiperInstance.value?.slideNext()
}
function slidePrev() {
  swiperInstance.value?.slidePrev()
}
function onSlideChange(swiper) {
  currentIndex.value = swiper.activeIndex
}

// Si cambian las reviews, resetea el índice
watch(() => props.reviews, () => {
  currentIndex.value = 0
})
</script>

<style scoped>
/* Oculta los botones de navegación nativos de Swiper */
.review-swiper :deep(.swiper-button-next),
.review-swiper :deep(.swiper-button-prev) {
  display: none !important;
}

/* Botones personalizados */
.swiper-custom-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: #fff;
  opacity: 0.8;
  border-radius: 9999px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  padding: 0.5rem;
  color: #431605;
  transition: opacity 0.2s;
  opacity: 0;
  pointer-events: none;
  z-index: 10;
}
.group:hover .swiper-custom-btn {
  opacity: 1;
  pointer-events: auto;
}
.swiper-custom-btn-prev {
  left: 0.5rem;
}
.swiper-custom-btn-next {
  right: 0.5rem;
}
</style>

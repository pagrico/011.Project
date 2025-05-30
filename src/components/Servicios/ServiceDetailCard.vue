<template>
  <div class="service-detail rounded-lg p-8 grid grid-cols-1 lg:grid-cols-2 gap-12">
    <div>
      <div class="flex items-center mb-6">
        <div class="service-icon mr-4"><i :class="['fas', service.icon]"></i></div>
        <h3 class="text-2xl font-bold text-[#431605]">{{ service.title }}</h3>
      </div>
      <p class="text-lg mb-6">{{ service.intro }}</p>
      <p class="mb-6">{{ service.description }}</p>
      <div class="bg-[#B7CDDA] bg-opacity-30 p-6 rounded-lg mb-8">
        <h4 class="font-bold text-[#431605] mb-4 text-lg">Incluye:</h4>
        <ul class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <li v-for="item in service.package || []" :key="item" class="flex items-start">
            <i class="fas fa-check text-[#BCAA81] mt-1 mr-2"></i>
            <span>{{ item }}</span>
          </li>
        </ul>
      </div>
      <div class="flex flex-col sm:flex-row items-center justify-between bg-[#DBE6ED] p-6 rounded-lg">
        <div class="mb-4 sm:mb-0">
          <span class="text-3xl font-bold text-[#431605]">{{ service.price }}</span>
          <span class="text-gray-600 ml-2">{{ service.priceNote }}</span>
        </div>
        <button @click="openContact(service.title)" class="btn-primary px-8 py-3 rounded-md font-medium">Contactar</button>
      </div>
      <ContactModal
        :visible="contactModalVisible"
        :serviceTitle="selectedServiceTitle"
        @close="contactModalVisible = false"
      />
      <!-- GALERÍA DE IMÁGENES: solo si hay imágenes -->
      <div v-if="service.images && service.images.length" class="mt-10">
        <h4 class="font-bold text-[#431605] mb-4 text-lg">Galería de trabajos anteriores</h4>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
          <div
            v-for="(img, idx) in service.images"
            :key="idx"
            class="rounded overflow-hidden border border-[#BCAA81] bg-white cursor-pointer"
            @click="openGallery(idx)"
          >
            <img :src="img" alt="Trabajo anterior" class="w-full h-40 object-cover hover:scale-105 transition-transform duration-200" loading="lazy" />
          </div>
        </div>
      </div>
    </div>
    <div>
      <h4 class="font-bold text-xl text-[#431605] mb-6">Reseñas de clientes</h4>
      <ReviewSwiper :reviews="service.reviews" />
      <ReviewForm :serviceId="service.id" @review-added="refreshReviews" />
    </div>
  </div>
  <teleport to="body">
    <div
      v-if="galleryOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-80"
    >
      <button
        class="absolute top-4 right-4 text-white text-3xl"
        @click="closeGallery"
        aria-label="Cerrar"
      >&times;</button>
      <button
        v-if="galleryIndex > 0"
        class="absolute left-4 top-1/2 -translate-y-1/2 text-white text-4xl px-2"
        @click="prevImage"
        aria-label="Anterior"
      >&lt;</button>
      <img
        :src="service.images[galleryIndex]"
        class="max-h-[80vh] max-w-[90vw] rounded shadow-lg"
        :alt="'Trabajo anterior ' + (galleryIndex + 1)"
      />
      <button
        v-if="galleryIndex < service.images.length - 1"
        class="absolute right-4 top-1/2 -translate-y-1/2 text-white text-4xl px-2"
        @click="nextImage"
        aria-label="Siguiente"
      >&gt;</button>
    </div>
  </teleport>
</template>

<script setup>
import ReviewSwiper from './ReviewSwiper.vue'
import ReviewForm from './ReviewForm.vue'
import ContactModal from './ContactModal.vue'
import { ref } from 'vue'
const props = defineProps(['service'])
const emit = defineEmits(['openContact', 'refresh'])
const contactModalVisible = ref(false)
const selectedServiceTitle = ref('')
function openContact(title) {
  selectedServiceTitle.value = title
  contactModalVisible.value = true
}
function refreshReviews() {
  emit('refresh')
}

// Lightbox gallery logic
const galleryOpen = ref(false)
const galleryIndex = ref(0)
function openGallery(idx) {
  galleryIndex.value = idx
  galleryOpen.value = true
}
function closeGallery() {
  galleryOpen.value = false
}
function prevImage() {
  if (galleryIndex.value > 0) galleryIndex.value--
}
function nextImage() {
  if (galleryIndex.value < props.service.images.length - 1) galleryIndex.value++
}

// Cerrar galería con ESC
if (typeof window !== 'undefined') {
  window.addEventListener('keydown', (e) => {
    if (galleryOpen.value) {
      if (e.key === 'Escape') closeGallery()
      if (e.key === 'ArrowLeft') prevImage()
      if (e.key === 'ArrowRight') nextImage()
    }
  })
}
</script>

<style scoped>
.service-detail {
  background: linear-gradient(135deg, white 0%, #DBE6ED 100%);
  border: 1px solid #B7CDDA;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
.price-tag {
  background-color: #BCAA81;
  color: #431605;
  position: absolute;
  top: 15px;
  right: 15px;
  transform: rotate(5deg);
}
.review-card {
  background-color: white;
  border-left: 4px solid #BCAA81;
  position: relative;
}
.review-card::before {
  content: '"';
  position: absolute;
  top: 10px;
  left: 10px;
  font-size: 60px;
  color: rgba(188, 170, 129, 0.1);
  font-family: Georgia, serif;
  line-height: 1;
}
.star-rating .star {
  color: #BCAA81;
}
.star-rating .star.empty {
  color: #B7CDDA;
}
</style>
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
          <div v-for="(img, idx) in service.images" :key="idx" class="rounded overflow-hidden border border-[#BCAA81] bg-white">
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
</script>
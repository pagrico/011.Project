<template>
  <div class="py-8 px-4 md:px-8 lg:px-16 max-w-7xl mx-auto mt-8">
    <!-- Panel de administración (usa v-if para acceso por rol) -->
    <AdminPanel
      v-if="isAdmin"
      :services="services"
      :loading="loading"
      @showSolicitudModal="showSolicitudModal"
    />
    <!-- Encabezado -->
    <HeaderSection />

    <!-- Grid de tarjetas de servicios -->
    <ServiceGrid :services="visibleServices" :loading="loading" @learnMore="handleLearnMore" />

    <!-- Swiper de detalles de servicios -->
    <ServiceDetailsSwiper ref="detailsSwiper" :services="visibleServices" :loading="loading" @refresh="fetchServices" />

    <!-- Modal de contacto global -->
    <ContactModal
      :visible="contactModalVisible"
      :serviceTitle="contactModalServiceTitle"
      @close="contactModalVisible = false"
    />

    <!-- Modal de solicitud global -->
    <SolicitudModal
      v-if="solicitudModalVisible"
      :solicitud="solicitudSeleccionada"
      @close="solicitudModalVisible = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import HeaderSection from '../components/Servicios/HeaderSection.vue'
import ServiceGrid from '../components/Servicios/ServiceGrid.vue'
import ServiceDetailsSwiper from '../components/Servicios/ServiceDetailsSwiper.vue'
import AdminPanel from '../components/Servicios/AdminPanel.vue'
import ContactModal from '../components/Servicios/ContactModal.vue'
import SolicitudModal from '../components/Servicios/SolicitudModal.vue'

const contactModalVisible = ref(false)
const contactModalServiceTitle = ref('')
const detailsSwiper = ref(null)

const solicitudModalVisible = ref(false)
const solicitudSeleccionada = ref(null)

const services = ref([])
const loading = ref(true)

const isAdmin = computed(() => {
  try {
    const userData = JSON.parse(localStorage.getItem('userData'))
    return userData && userData.role === 1
  } catch {
    return false
  }
})

// Solo servicios activos para mostrar al usuario
const visibleServices = computed(() => services.value.filter(s => s.status === 'Activo'))

function handleLearnMore(serviceId) {
  detailsSwiper.value?.scrollToService(serviceId)
}

// Permite mostrar el modal de solicitud desde hijos
function showSolicitudModal(solicitud) {
  solicitudSeleccionada.value = solicitud
  solicitudModalVisible.value = true
}

async function fetchServices() {
  loading.value = true
  try {
    const res = await fetch('http://localhost:8080/Apis/API_servicios_listado.php')
    const data = await res.json()
    services.value = (data.servicios || []).map(s => {
      let incluyeArr = []
      if (Array.isArray(s.SER_INCLUYE)) {
        incluyeArr = s.SER_INCLUYE
      } else if (typeof s.SER_INCLUYE === 'string' && s.SER_INCLUYE.trim().startsWith('[')) {
        try {
          incluyeArr = JSON.parse(s.SER_INCLUYE)
        } catch {
          incluyeArr = []
        }
      }
      return {
        id: s.SER_ID,
        icon: s.SER_ICONO,
        title: s.SER_TITULO,
        desc: s.SER_DESCRIPCION_CORTA,
        description: s.SER_DESCRIPCION_LARGA,
        price: s.SER_PRECIO ? (parseFloat(s.SER_PRECIO).toLocaleString('es-ES', { style: 'currency', currency: 'EUR', minimumFractionDigits: 0 })) : '',
        priceRaw: s.SER_PRECIO,
        package: incluyeArr,
        reviews: (s.valoraciones || []).map(v => ({
          author: v.VAL_NOMBRE_USUARIO,
          rating: v.VAL_ESTRELLAS ?? 5,
          text: v.VAL_VALORACION
        })),
        status: s.SER_ESTADO
      }
    })
  } catch (e) {
    services.value = []
  }
  loading.value = false
}

onMounted(fetchServices)
</script>
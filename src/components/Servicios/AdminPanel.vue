<template>
  <section class="admin-panel rounded-lg p-8 mb-8">
    <div class="flex justify-between items-center mb-8">
      <h2 class="text-2xl font-bold text-[#431605]">
        Panel de Administración
      </h2>
      <button
        @click="togglePanel"
        class="ml-2 focus:outline-none transition-transform duration-300"
        aria-label="Expandir/Colapsar"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6 transform transition-transform duration-300"
          :class="expanded ? 'rotate-0' : 'rotate-180'"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
      </button>
    </div>
    <transition name="fade">
      <div v-show="expanded">
        <AdminTabs :activeTab="activeTab" @tab-change="activeTab = $event" />
        <StatsOverview
          :activeServices="activeServices"
          :pendingRequests="pendingRequests"
          :averageRating="averageRating"
          :monthRevenue="monthRevenue"
        />
        <ServicesTab
          v-if="activeTab === 'servicios'"
          :services="services"
          :loading="loading"
          @update="refreshServices"
        />
        <RequestsTab
          v-else-if="activeTab === 'requests'"
          @showSolicitudModal="emitShowSolicitudModal"
        />
        <AllRequestsTab
          v-if="activeTab === 'all-requests'"
          @showSolicitudModal="emitShowSolicitudModal"
        />
      </div>
    </transition>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import AdminTabs from './AdminTabs.vue'
import StatsOverview from './StatsOverview.vue'
import ServicesTab from './ServicesTab.vue'
import RequestsTab from './RequestTab.vue'
import AllRequestsTab from './AllRequestsTab.vue'

const props = defineProps(['services', 'loading'])
const emit = defineEmits(['showSolicitudModal'])
const expanded = ref(true)
const activeTab = ref('servicios')

// --- NUEVO: Estados para solicitudes y estadísticas ---
const solicitudes = ref([])
const solicitudesLoading = ref(false)

// Cargar solicitudes al montar o cuando se cambia a la pestaña de solicitudes
async function fetchSolicitudes() {
  solicitudesLoading.value = true
  try {
    const res = await fetch('http://localhost:8080/Apis/API_solicitudes_listado.php')
    const data = await res.json()
    if (data.success) {
      solicitudes.value = data.solicitudes
    } else {
      solicitudes.value = []
    }
  } catch {
    solicitudes.value = []
  }
  solicitudesLoading.value = false
}
onMounted(fetchSolicitudes)
watch(activeTab, (tab) => {
  if (tab === 'requests' || tab === 'all-requests') fetchSolicitudes()
})

// --- Estadísticas dinámicas ---
const activeServices = computed(() => (props.services || []).filter(s => s.status === 'Activo').length)
const pendingRequests = computed(() =>
  (solicitudes.value || []).filter(s => s.SOL_ESTADO === 'Pendiente').length
)
const averageRating = computed(() => {
  // Saca todas las valoraciones de todos los servicios
  const allReviews = (props.services || []).flatMap(s => Array.isArray(s.reviews) ? s.reviews : [])
  if (!allReviews.length) return 0
  const sum = allReviews.reduce((acc, r) => acc + (Number(r.rating) || 0), 0)
  return (sum / allReviews.length).toFixed(1)
})
const monthRevenue = computed(() => {
  // Suma los precios de las solicitudes aceptadas/finalizadas de este mes
  const now = new Date()
  const thisMonth = now.getMonth()
  const thisYear = now.getFullYear()
  let total = 0
  for (const sol of solicitudes.value || []) {
    if (
      ['Aceptado', 'Aceptada', 'Finalizado', 'Finalizada'].includes(sol.SOL_ESTADO)
      && sol.SOL_FECHA
    ) {
      let fecha = sol.SOL_FECHA
      if (!isNaN(Number(fecha))) fecha = Number(fecha)
      else if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(fecha)) fecha = fecha.replace(' ', 'T')
      const d = new Date(fecha)
      if (d.getMonth() === thisMonth && d.getFullYear() === thisYear) {
        // Busca el servicio relacionado para obtener el precio
        let precio = 0
        const servicio = (props.services || []).find(s =>
          sol.SOL_MENSAJE && s.title && sol.SOL_MENSAJE.includes(s.title)
        )
        if (servicio && servicio.priceRaw) {
          precio = Number(servicio.priceRaw) || 0
        }
        total += precio
      }
    }
  }
  // Formatea como moneda
  return total > 0 ? total.toLocaleString('es-ES', { style: 'currency', currency: 'EUR', minimumFractionDigits: 0 }) : '0 €'
})

function togglePanel() {
  expanded.value = !expanded.value
}

function refreshServices() {
  // Aquí iría la lógica para recargar servicios tras creación/edición/borrado.
  // Por ejemplo, emitir un evento al padre o llamar a una API.
}

// Nuevo: emitir al padre para mostrar el modal global
function emitShowSolicitudModal(solicitud) {
  emit('showSolicitudModal', solicitud)
}
</script>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
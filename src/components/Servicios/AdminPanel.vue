<template>
  <div>
    <!-- Panel de administración de servicios con pestañas y estadísticas -->
    <section
      class="admin-panel rounded-lg mb-8"
      :class="{ 'admin-panel-collapsed': !expanded }"
    >
      <div
        class="flex justify-between items-center mb-8 admin-panel-header"
        :class="{ 'admin-panel-header-collapsed': !expanded }"
        @click="!expanded && togglePanel()"
        style="cursor: pointer;"
      >
        <div class="flex items-center gap-2">
          <span v-if="!expanded" class="admin-panel-icon">
            <!-- Icono de herramientas (FontAwesome o SVG) -->
          </span>
          <h2 class="text-2xl font-bold text-[#431605] mb-0 select-none">
            Panel de Administración
          </h2>
        </div>
        <button
          @click.stop="togglePanel"
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
          <!-- Pestañas de administración -->
          <AdminTabs :activeTab="activeTab" @tab-change="activeTab = $event" />
          <!-- Estadísticas generales -->
          <StatsOverview
            :activeServices="activeServices"
            :pendingRequests="pendingRequests"
            :averageRating="averageRating"
            :monthRevenue="monthRevenue"
          />
          <!-- Gestión de servicios -->
          <ServicesTab
            v-if="activeTab === 'servicios'"
            :services="services"
            :loading="loading"
            @update="refreshServices"
          />
          <!-- Solicitudes pendientes -->
          <RequestsTab
            v-else-if="activeTab === 'requests'"
            @showSolicitudModal="emitShowSolicitudModal"
          />
          <!-- Todas las solicitudes -->
          <AllRequestsTab
            v-if="activeTab === 'all-requests'"
            @showSolicitudModal="emitShowSolicitudModal"
          />
        </div>
      </transition>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, inject } from 'vue'
import AdminTabs from './AdminTabs.vue'
import StatsOverview from './StatsOverview.vue'
import ServicesTab from './ServicesTab.vue'
import RequestsTab from './RequestTab.vue'
import AllRequestsTab from './AllRequestsTab.vue'

const props = defineProps(['services', 'loading'])
const emit = defineEmits(['showSolicitudModal'])
const expanded = ref(false)
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
  // Formatea como moneda usando punto y coma como separador
  return total > 0 ? total.toLocaleString('es-ES', { 
    style: 'currency', 
    currency: 'EUR', 
    minimumFractionDigits: 0,
    useGrouping: true
  }).replace(/\./g, ";") : '0 €'
})

function togglePanel() {
  expanded.value = !expanded.value
}

// Nuevo: emitir al padre para mostrar el modal global
function emitShowSolicitudModal(solicitud) {
  emit('showSolicitudModal', solicitud)
}
</script>

<style scoped>
.admin-panel {
  background: linear-gradient(135deg, #B7CDDA 0%, #a0b9c9 100%);
  border-top: 3px solid #431605;
  box-shadow: inset 0 10px 20px rgba(0, 0, 0, 0.05);
  padding: 2rem;
  transition: background 0.3s, box-shadow 0.3s, padding 0.3s, border-left 0.3s;
}
.admin-panel-collapsed {
  background: rgba(255,255,255,0.85);
  box-shadow: 0 4px 18px 0 rgba(188, 170, 129, 0.18), 0 1.5px 6px 0 rgba(67, 22, 5, 0.10);
  padding: 0.5rem 1.5rem;
  border-top: 2px solid #b7b7b7;
  border-left: 7px solid #BCAA81;
  /* Resalta el panel cerrado */
  filter: drop-shadow(0 2px 8px #BCAA81aa);
}
.admin-panel-header {
  transition: margin 0.3s, padding 0.3s;
}
.admin-panel-header-collapsed {
  margin-bottom: 0 !important;
  padding: 0.5rem 0;
}
.admin-panel-icon {
  display: inline-flex;
  align-items: center;
  margin-right: 0.5rem;
  opacity: 0.85;
  /* Puedes ajustar el tamaño aquí si lo deseas */
}
.admin-card {
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}
.admin-card-header {
  background: linear-gradient(90deg, #BCAA81, #C18F67);
  color: white;
  font-family: 'Playfair Display', serif;
  padding: 1rem 1.5rem;
}
.admin-tab {
  padding: 0.75rem 1.5rem;
  cursor: pointer;
  border-bottom: 3px solid transparent;
  transition: all 0.3s ease;
  border-bottom: 3px solid transparent;
}
.admin-tab:hover, .admin-tab.active {
  border-bottom-color: #825336;
  color: #431605;
  font-weight: 500;
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
.btn-secondary {
  background-color: #B7CDDA;
  color: #431605;
  transition: all 0.3s ease;
}
.btn-secondary:hover {
  background-color: #BCAA81;
}
.status-pending {
  background-color: #FEF3C7;
  color: #92400E;
}
.status-accepted {
  background-color: #D1FAE5;
  color: #065F46;
}
.status-rejected {
  background-color: #FEE2E2;
  color: #92400E;
}
.status-finished {
  background-color: #DBEAFE;
  color: #1E40AF;
}
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>
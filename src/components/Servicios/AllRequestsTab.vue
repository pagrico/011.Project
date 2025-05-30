<template>
  <div class="admin-card rounded-lg overflow-hidden">
    <div class="admin-card-header px-6 py-4">
      <h3 class="text-lg font-bold">Todas las solicitudes de servicios</h3>
    </div>
    <div class="p-6">
      <div class="mb-4 flex justify-between items-center">
        <div class="flex space-x-2">
          <select v-model="filtroEstado" class="px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
            <option value="">Todos los estados</option>
            <option value="Pendiente">Pendiente</option>
            <option value="Aceptado">Aceptado</option>
            <option value="Rechazado">Rechazado</option>
            <option value="Finalizado">Finalizado</option>
          </select>
          <input v-model="filtroBusqueda" type="text" placeholder="Buscar..." class="px-3 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
        </div>
        <button @click="exportarCSV" class="btn-primary px-4 py-1 rounded-md font-medium">Exportar</button>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg overflow-hidden">
          <thead class="bg-[#DBE6ED]">
            <tr>
              <th class="px-4 py-2 text-left">Servicio</th>
              <th class="px-4 py-2 text-left">Cliente</th>
              <th class="px-4 py-2 text-left">Correo</th>
              <th class="px-4 py-2 text-left">Fecha</th>
              <th class="px-4 py-2 text-left">Estado</th>
              <th class="px-4 py-2 text-left">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b border-gray-200 hover:bg-gray-50" v-for="req in solicitudesPaginadas" :key="req.SOL_ID">
              <td class="px-4 py-3">{{ extraerServicio(req.SOL_MENSAJE) }}</td>
              <td class="px-4 py-3">{{ req.SOL_NOMBRE }}</td>
              <td class="px-4 py-3">{{ req.SOL_CORREO }}</td>
              <td class="px-4 py-3">{{ formatearFecha(req.SOL_FECHA) }}</td>
              <td class="px-4 py-3">
                <span :class="statusClass(req.SOL_ESTADO) + ' px-2 py-1 rounded-full text-xs'">{{ req.SOL_ESTADO }}</span>
              </td>
              <td class="px-4 py-3">
                <button class="text-blue-600 hover:text-blue-800" @click="ver(req)">
                  <i class="fas fa-eye"></i>
                </button>
              </td>
            </tr>
            <tr v-if="solicitudesPaginadas.length === 0">
              <td colspan="6" class="text-center text-gray-500 py-8">No hay resultados.</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4 flex justify-between items-center">
        <div class="text-sm text-gray-600">
          Mostrando {{ inicio + 1 }} a {{ fin }} de {{ solicitudesFiltradas.length }} entradas
        </div>
        <div class="flex space-x-1">
          <button
            v-for="n in totalPaginas"
            :key="n"
            :class="['px-3 py-1 border border-gray-300 rounded-md', paginaActual === n ? 'bg-[#BCAA81] text-white' : 'hover:bg-gray-100']"
            @click="cambiarPagina(n)"
          >{{ n }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'

const emit = defineEmits(['showSolicitudModal'])

const solicitudes = ref([])
const filtroEstado = ref('')
const filtroBusqueda = ref('')
const paginaActual = ref(1)
const porPagina = 5

function statusClass(status) {
  if (status === 'Pendiente') return 'status-pending'
  if (status === 'Aceptada' || status === 'Aceptado') return 'status-accepted'
  if (status === 'Finalizada' || status === 'Finalizado') return 'status-finished'
  if (status === 'Rechazada' || status === 'Rechazado') return 'status-rejected'
  return ''
}
function formatearFecha(fecha) {
  if (!fecha) return ''
  if (!isNaN(Number(fecha))) {
    const d = new Date(Number(fecha))
    if (!isNaN(d.getTime())) return d.toLocaleString('es-ES')
  }
  let f = fecha
  if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(fecha)) {
    f = fecha.replace(' ', 'T')
  }
  const d = new Date(f)
  if (!isNaN(d.getTime())) return d.toLocaleString('es-ES')
  return fecha
}
function extraerServicio(mensaje) {
  if (!mensaje) return ''
  const match = mensaje.match(/^\[Servicio: (.+?)\]/)
  return match ? match[1] : 'General'
}
function ver(req) {
  emit('showSolicitudModal', req)
}
async function fetchSolicitudes() {
  try {
    const res = await fetch('http://localhost:8080/Apis/API_solicitudes_listado.php')
    const data = await res.json()
    if (data.success) {
      solicitudes.value = data.solicitudes
    }
  } catch {}
}

// Filtrado doble: por estado y búsqueda
const solicitudesFiltradas = computed(() => {
  let arr = solicitudes.value
  if (filtroEstado.value) {
    arr = arr.filter(s => (s.SOL_ESTADO || '').toLowerCase() === filtroEstado.value.toLowerCase())
  }
  if (filtroBusqueda.value) {
    const q = filtroBusqueda.value.toLowerCase()
    arr = arr.filter(s =>
      (s.SOL_NOMBRE && s.SOL_NOMBRE.toLowerCase().includes(q)) ||
      (s.SOL_CORREO && s.SOL_CORREO.toLowerCase().includes(q)) ||
      (s.SOL_MENSAJE && s.SOL_MENSAJE.toLowerCase().includes(q)) ||
      (s.SOL_ESTADO && s.SOL_ESTADO.toLowerCase().includes(q))
    )
  }
  return arr
})

// Paginación
const totalPaginas = computed(() => Math.max(1, Math.ceil(solicitudesFiltradas.value.length / porPagina)))
const inicio = computed(() => (paginaActual.value - 1) * porPagina)
const fin = computed(() => Math.min(inicio.value + porPagina, solicitudesFiltradas.value.length))
const solicitudesPaginadas = computed(() =>
  solicitudesFiltradas.value.slice(inicio.value, fin.value)
)

function cambiarPagina(n) {
  if (n >= 1 && n <= totalPaginas.value) {
    paginaActual.value = n
  }
}

// Resetear a la página 1 si cambian los filtros
watch([filtroEstado, filtroBusqueda], () => {
  paginaActual.value = 1
})

// Exportar CSV de los datos filtrados
function exportarCSV() {
  const rows = [
    ['Servicio', 'Cliente', 'Correo', 'Fecha', 'Estado'],
    ...solicitudesFiltradas.value.map(s => [
      extraerServicio(s.SOL_MENSAJE),
      s.SOL_NOMBRE,
      s.SOL_CORREO,
      formatearFecha(s.SOL_FECHA),
      s.SOL_ESTADO
    ])
  ]
  const csv = rows.map(r => r.map(v => `"${(v ?? '').toString().replace(/"/g, '""')}"`).join(',')).join('\r\n')
  const blob = new Blob([csv], { type: 'text/csv' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = 'solicitudes.csv'
  document.body.appendChild(a)
  a.click()
  setTimeout(() => {
    document.body.removeChild(a)
    URL.revokeObjectURL(url)
  }, 100)
}

import { watch } from 'vue'
onMounted(fetchSolicitudes)
</script>

<style scoped>
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
</style>
<template>
  <div class="admin-card rounded-lg overflow-hidden">
    <div class="admin-card-header px-6 py-4">
      <h3 class="text-lg font-bold">Solicitudes de servicios pendientes</h3>
    </div>
    <div class="p-6">
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg overflow-hidden" v-if="solicitudes.length > 0">
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
            <tr class="border-b border-gray-200 hover:bg-gray-50" v-for="req in solicitudes" :key="req.SOL_ID">
              <td class="px-4 py-3">{{ extraerServicio(req.SOL_MENSAJE) }}</td>
              <td class="px-4 py-3">{{ req.SOL_NOMBRE }}</td>
              <td class="px-4 py-3">{{ req.SOL_CORREO }}</td>
              <td class="px-4 py-3">
                <span v-if="req.SOL_FECHA">
                  {{ mostrarFecha(req.SOL_FECHA) }}
                </span>
                <span v-else class="text-red-500 text-xs">Sin fecha</span>
              </td>
              <td class="px-4 py-3">
                <span :class="statusClass(req.SOL_ESTADO) + ' px-2 py-1 rounded-full text-xs'">{{ req.SOL_ESTADO }}</span>
              </td>
              <td class="px-4 py-3">
                <button v-if="req.SOL_ESTADO === 'Pendiente'" class="text-green-600 hover:text-green-800 mr-2" @click="cambiarEstado(req, 'Aceptado')">
                  <i class="fas fa-check"></i>
                </button>
                <button v-if="req.SOL_ESTADO === 'Pendiente'" class="text-red-600 hover:text-red-800 mr-2" @click="cambiarEstado(req, 'Rechazado')">
                  <i class="fas fa-times"></i>
                </button>
                <button v-if="req.SOL_ESTADO === 'Aceptado'" class="text-yellow-600 hover:text-yellow-800 mr-2" @click="cambiarEstado(req, 'Finalizado')">
                  <i class="fas fa-flag-checkered"></i>
                </button>
                <button class="text-blue-600 hover:text-blue-800" @click="ver(req)">
                  <i class="fas fa-eye"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
        <div v-else class="text-center text-gray-500 py-8">
          {{ errorMsg ? errorMsg : 'No hay solicitudes pendientes o aceptadas.' }}
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'

// Inyectar la función de alertas desde App.vue
const showAlert = inject('showAlert')

const emit = defineEmits(['showSolicitudModal'])

const solicitudes = ref([])
const errorMsg = ref('')

function statusClass(status) {
  if (status === 'Pendiente') return 'status-pending'
  if (status === 'Aceptado' || status === 'Aceptada') return 'status-accepted'
  if (status === 'Finalizado' || status === 'Finalizada') return 'status-finished'
  if (status === 'Rechazado' || status === 'Rechazada') return 'status-rejected'
  return ''
}
function formatearFecha(fecha) {
  if (!fecha) return ''
  // Si ya es un número, asume timestamp
  if (!isNaN(Number(fecha))) {
    const d = new Date(Number(fecha))
    if (!isNaN(d.getTime())) return d.toLocaleString('es-ES')
  }
  // Si es string tipo '2024-06-07 13:45:00', reemplaza espacio por T
  let f = fecha
  if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(fecha)) {
    f = fecha.replace(' ', 'T')
  }
  const d = new Date(f)
  if (!isNaN(d.getTime())) return d.toLocaleString('es-ES')
  // Si sigue fallando, muestra el valor original
  return fecha
}
// Nueva función para mostrar la fecha siempre
function mostrarFecha(fecha) {
  if (!fecha) return ''
  // Si ya es un número, asume timestamp
  if (!isNaN(Number(fecha))) {
    const d = new Date(Number(fecha))
    if (!isNaN(d.getTime())) return d.toLocaleDateString('es-ES')
  }
  // Si es string tipo '2024-06-07 13:45:00', reemplaza espacio por T
  let f = fecha
  if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(fecha)) {
    f = fecha.replace(' ', 'T')
  }
  const d = new Date(f)
  if (!isNaN(d.getTime())) return d.toLocaleDateString('es-ES')
  // Si sigue fallando, muestra el valor original
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
  errorMsg.value = ''
  try {
    const res = await fetch('http://localhost:8080/Apis/API_solicitudes_listado.php')
    const data = await res.json()
    if (data.success) {
      solicitudes.value = data.solicitudes.filter(s =>
        ['Pendiente', 'Aceptado', 'Aceptada'].includes(s.SOL_ESTADO)
      )
    } else {
      errorMsg.value = 'Error al cargar solicitudes: ' + (data.error || '')
      solicitudes.value = []
    }
  } catch (e) {
    errorMsg.value = 'No se pudo conectar con el servidor.'
    solicitudes.value = []
  }
}
async function cambiarEstado(req, nuevoEstado) {
  try {
    // Cambia la URL al puerto correcto de tu backend PHP
    const response = await fetch('http://localhost:8080/Apis/API_solicitud_estado.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: req.SOL_ID, estado: nuevoEstado })
    })
    
    const data = await response.json()
    
    if (data.success) {
      showAlert(`Solicitud marcada como ${nuevoEstado} correctamente`, true)
      fetchSolicitudes()
    } else {
      showAlert(`Error al cambiar estado: ${data.error || 'Error desconocido'}`, false)
    }
  } catch (e) {
    showAlert('No se pudo conectar con el servidor para actualizar el estado.', false)
    errorMsg.value = 'No se pudo conectar con el servidor para actualizar el estado.'
  }
}
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
</style>
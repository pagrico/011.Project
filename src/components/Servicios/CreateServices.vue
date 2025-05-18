<template>
  <div class="admin-card rounded-lg overflow-hidden">
    <div class="admin-card-header px-6 py-4">
      <h3 class="text-lg font-bold">{{ isEditMode ? 'Editar servicio' : 'Crear nuevo servicio' }}</h3>
    </div>
    <div class="p-6">
      <form @submit.prevent="onSubmit">
        <div class="mb-4">
          <label for="serviceTitle" class="block text-gray-700 mb-2 font-medium">Título</label>
          <input v-model="title" type="text" id="serviceTitle" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
        </div>
        <div class="mb-4">
          <label for="serviceShortDesc" class="block text-gray-700 mb-2 font-medium">Descripción corta</label>
          <textarea v-model="descripcion_corta" id="serviceShortDesc" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]"></textarea>
        </div>
        <div class="mb-4">
          <label for="serviceLongDesc" class="block text-gray-700 mb-2 font-medium">Descripción larga</label>
          <textarea v-model="descripcion_larga" id="serviceLongDesc" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]"></textarea>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-2 font-medium">Incluye</label>
          <div class="flex mb-2">
            <input v-model="nuevoIncluye" type="text" placeholder="Añadir elemento" class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
            <button type="button" @click="agregarIncluye" class="ml-2 px-4 py-2 bg-[#BCAA81] text-white rounded-md">Añadir</button>
          </div>
          <ul class="pl-2">
            <li v-for="(item, idx) in incluye" :key="idx" class="flex items-center justify-between mb-1">
              <span class="flex items-center">
                <span class="inline-block w-2 h-2 rounded-full bg-[#BCAA81] mr-2"></span>
                {{ item }}
              </span>
              <button type="button" @click="eliminarIncluye(idx)" class="ml-2 text-red-500 hover:text-red-700">Quitar</button>
            </li>
          </ul>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 mb-2 font-medium">Estado</label>
          <div class="flex items-center">
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="estadoActivo" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[#BCAA81] rounded-full peer peer-checked:bg-[#BCAA81] transition-all"></div>
              <span class="ml-3 text-sm font-medium text-gray-900">{{ estadoActivo ? 'Activo' : 'Oculto' }}</span>
            </label>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label for="servicePrice" class="block text-gray-700 mb-2 font-medium">Precio (€)</label>
            <input v-model.number="price" type="number" id="servicePrice" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
          </div>
        </div>
        <div class="mb-4">
          <label for="serviceIcon" class="block text-gray-700 mb-2 font-medium">Icono</label>
          <div class="flex items-center space-x-3">
            <select v-model="icon" id="serviceIcon" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#BCAA81]">
              <option v-for="option in iconOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </option>
            </select>
            <span v-if="icon">
              <i :class="['fas', icon, 'text-2xl', 'text-[#BCAA81]']"></i>
            </span>
          </div>
        </div>
        <div class="flex items-center justify-between">
          <button type="submit" class="btn-primary px-6 py-2 rounded-md font-medium">
            {{ isEditMode ? 'Actualizar' : 'Crear servicio' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
const emit = defineEmits(['created', 'updated'])
const props = defineProps({
  serviceToEdit: {
    type: Object,
    default: null
  }
})

const iconOptions = [
  { value: 'fa-camera-retro', label: 'Fotografía' },
  { value: 'fa-video', label: 'Video' },
  { value: 'fa-paint-brush', label: 'Diseño gráfico' },
  { value: 'fa-laptop-code', label: 'Desarrollo web' },
  { value: 'fa-mobile-alt', label: 'Desarrollo móvil' },
  { value: 'fa-search', label: 'SEO' },
  { value: 'fa-bullhorn', label: 'Marketing' },
  { value: 'fa-pen-fancy', label: 'Redacción' },
  { value: 'fa-headset', label: 'Soporte técnico' },
  { value: 'fa-graduation-cap', label: 'Formación' },
  { value: 'fa-briefcase', label: 'Consultoría' },
  { value: 'fa-chart-line', label: 'Analítica' },
  { value: 'fa-shield-alt', label: 'Ciberseguridad' },
  { value: 'fa-cloud', label: 'Cloud' },
  { value: 'fa-users', label: 'Recursos humanos' },
  { value: 'fa-truck', label: 'Logística' },
  { value: 'fa-hands-helping', label: 'Asesoría' },
  { value: 'fa-bug', label: 'Testing' },
  { value: 'fa-calendar-alt', label: 'Gestión de eventos' },
  { value: 'fa-file-invoice-dollar', label: 'Facturación' }
]

const isEditMode = computed(() => !!props.serviceToEdit)

const title = ref('')
const descripcion_corta = ref('')
const descripcion_larga = ref('')
const incluye = ref([])
const nuevoIncluye = ref('')
const price = ref(0)
const icon = ref('fa-camera-retro')
const estadoActivo = ref(false)
const estado = ref('Oculto')

watch(estadoActivo, (val) => {
  estado.value = val ? 'Activo' : 'Oculto'
})

// Rellenar campos si se está editando
watch(() => props.serviceToEdit, (val, oldVal) => {
  if (val && val !== oldVal) {
    // Mostrar el objeto completo recibido al editar
    console.log('Objeto recibido en serviceToEdit:', val)
    // Título
    title.value = val.titulo || val.title || val.SER_TITULO || ''
    // Descripción corta
    descripcion_corta.value = val.descripcion_corta || val.SER_DESCRIPCION_CORTA || val.desc || ''
    // Descripción larga
    descripcion_larga.value = val.descripcion_larga || val.SER_DESCRIPCION_LARGA || val.description || ''
    // Incluye (array o string JSON o package)
    if (Array.isArray(val.incluye)) {
      incluye.value = [...val.incluye]
    } else if (Array.isArray(val.package)) {
      incluye.value = [...val.package]
    } else if (typeof val.incluye === 'string' && val.incluye.trim().startsWith('[')) {
      try {
        incluye.value = JSON.parse(val.incluye)
      } catch {
        incluye.value = []
      }
    } else {
      incluye.value = []
    }
    // Mostrar el contenido del array incluye en consola al editar
    console.log('Array incluye al editar:', incluye.value)
    // Precio
    price.value = val.precio ?? val.price ?? val.SER_PRECIO ?? 0
    // Icono
    icon.value = val.icono || val.icon || val.SER_ICONO || 'fa-camera-retro'
    // Estado
    estadoActivo.value = (val.estado ?? val.status ?? val.SER_ESTADO) === 'Activo'
    estado.value = val.estado ?? val.status ?? val.SER_ESTADO ?? 'Oculto'
  } else if (!val) {
    // Limpiar campos si no hay servicio a editar
    title.value = ''
    descripcion_corta.value = ''
    descripcion_larga.value = ''
    incluye.value = []
    price.value = 0
    icon.value = 'fa-camera-retro'
    estadoActivo.value = false
    estado.value = 'Oculto'
  }
}, { immediate: true })

function agregarIncluye() {
  if (nuevoIncluye.value.trim() !== '') {
    incluye.value.push(nuevoIncluye.value.trim())
    nuevoIncluye.value = ''
  }
}
function eliminarIncluye(idx) {
  incluye.value.splice(idx, 1)
}

async function onSubmit() {
  if (isEditMode.value) {
    await updateService()
  } else {
    await createService()
  }
}

async function createService() {
  const body = {
    icono: icon.value,
    precio: price.value,
    titulo: title.value,
    descripcion_corta: descripcion_corta.value,
    descripcion_larga: descripcion_larga.value,
    incluye: JSON.stringify(incluye.value),
    estado: estado.value
  }
  try {
    const res = await fetch('http://localhost:8080/Apis/API_crear_servicio.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    })
    const data = await res.json()
    if (data.success) {
      emit('created')
      // Limpiar campos
      title.value = ''
      descripcion_corta.value = ''
      descripcion_larga.value = ''
      incluye.value = []
      price.value = 0
      icon.value = 'fa-camera-retro'
      estadoActivo.value = false
      nuevoIncluye.value = ''
      window.location.reload()
    } else {
      alert('Error al crear servicio: ' + data.error)
    }
  } catch (e) {
    alert('Error de red')
  }
}

async function updateService() {
  const body = {
    id: props.serviceToEdit.id || props.serviceToEdit.SER_ID,
    icono: icon.value,
    precio: price.value,
    titulo: title.value,
    descripcion_corta: descripcion_corta.value,
    descripcion_larga: descripcion_larga.value,
    incluye: JSON.stringify(incluye.value),
    estado: estado.value
  }
  try {
    const res = await fetch('http://localhost:8080/Apis/API_editar_servicio.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    })
    const data = await res.json()
    if (data.success) {
      emit('updated')
      // Limpiar campos
      title.value = ''
      descripcion_corta.value = ''
      descripcion_larga.value = ''
      incluye.value = []
      price.value = 0
      icon.value = 'fa-camera-retro'
      estadoActivo.value = false

      window.location.reload()
    } else {
      alert('Error al actualizar servicio: ' + data.error)
    }
  } catch (e) {
    alert('Error de red')
  }
}
</script>
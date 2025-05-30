<template>
  <div class="admin-card rounded-lg overflow-hidden">
    <div class="admin-card-header px-6 py-4">
      <h3 class="text-lg font-bold">Gestionar servicios</h3>
    </div>
    <div class="p-6">
      <!-- El formulario de creación/edición está en el padre -->
      <div class="mb-8">
        <h4 class="font-bold text-[#431605] mb-4">Todos los servicios</h4>
        <div v-if="loading" class="text-center py-6">Cargando servicios...</div>
        <div v-else class="overflow-x-auto">
          <table class="min-w-full bg-white rounded-lg overflow-hidden">
            <thead class="bg-[#DBE6ED]">
              <tr>
                <th class="px-4 py-2 text-left">Título</th>
                <th class="px-4 py-2 text-left">Precio</th>
                <th class="px-4 py-2 text-left">Estado</th>
                <th class="px-4 py-2 text-left">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="service in services" :key="service.id" class="border-b border-gray-200 hover:bg-gray-50">
                <td class="px-4 py-3">{{ service.title }}</td>
                <td class="px-4 py-3">{{ service.price }}</td>
                <td class="px-4 py-3">
                  <span :class="service.status === 'Activo' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded-full text-xs">
                    {{ service.status }}
                  </span>
                </td>
                <td class="px-4 py-3 text-right">
                  <button class="text-blue-600 hover:text-blue-800 mr-2" @click="editService(service)">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="text-red-600 hover:text-red-800" @click="openDeleteModal(service)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <!-- Modal de confirmación de borrado -->
  <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full relative">
      <h2 class="text-lg font-bold mb-4 text-[#431605]">¿Estás seguro?</h2>
      <p class="mb-6 text-gray-700">
        Esta acción no se puede deshacer.<br>
        ¿Deseas eliminar este servicio?
      </p>
      <div class="flex justify-end space-x-3">
        <button @click="showDeleteModal = false" class="px-4 py-2 rounded bg-gray-200 text-gray-700 hover:bg-gray-300">Cancelar</button>
        <button @click="confirmDelete" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">Eliminar</button>
      </div>
      <button @click="showDeleteModal = false" class="absolute top-2 right-3 text-gray-400 hover:text-gray-600 text-xl">&times;</button>
    </div>
  </div>
</template>

<script setup>
import { ref, inject } from 'vue'
const props = defineProps(['services', 'loading'])
const emit = defineEmits(['update', 'edit'])

const showDeleteModal = ref(false)
const serviceToDelete = ref(null)

// Inyectar la función de alertas desde App.vue
const showAlert = inject('showAlert')

function openDeleteModal(service) {
  serviceToDelete.value = service
  showDeleteModal.value = true
}

async function confirmDelete() {
  if (!serviceToDelete.value) return
  try {
    const id = serviceToDelete.value.id ?? serviceToDelete.value.SER_ID
    const res = await fetch('http://localhost:8080/Apis/API_eliminar_servicio.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    })
    const data = await res.json()
    if (data.success) {
      emit('update')
      showAlert('El servicio se ha eliminado correctamente.', true)
      // Espera a que el usuario cierre la alerta antes de recargar
      setTimeout(() => {
        window.location.reload()
      }, 1500) // Ajusta el tiempo según la duración de la alerta
    } else {
      showAlert('Error al eliminar: ' + (data.error || ''), false)
    }
  } catch (e) {
    showAlert('Error de red al eliminar', false)
  } finally {
    showDeleteModal.value = false
    serviceToDelete.value = null
  }
  
}

function parsePrice(value) {
  if (typeof value === 'number') return value
  if (typeof value === 'string') {
    // Elimina todo lo que no sea número o punto decimal
    const clean = value.replace(/[^\d.]/g, '')
    return Number(clean) || 0
  }
  return 0
}

function editService(service) {
  // Normaliza incluye a array
  let incluyeArr = []
  if (Array.isArray(service.package) && service.package.length > 0) {
    incluyeArr = service.package
  } else if (Array.isArray(service.incluye)) {
    incluyeArr = service.incluye
  } else if (typeof service.incluye === 'string' && service.incluye.trim().startsWith('[')) {
    try {
      incluyeArr = JSON.parse(service.incluye)
    } catch {
      incluyeArr = []
    }
  } else if (service.SER_INCLUYE && typeof service.SER_INCLUYE === 'string' && service.SER_INCLUYE.trim().startsWith('[')) {
    try {
      incluyeArr = JSON.parse(service.SER_INCLUYE)
    } catch {
      incluyeArr = []
    }
  }

  // Añade la propiedad imagenes (array de URLs)
  let imagenesArr = []
  if (Array.isArray(service.images)) {
    imagenesArr = service.images
  } else if (typeof service.imagenes === 'string' && service.imagenes.trim().startsWith('[')) {
    try {
      imagenesArr = JSON.parse(service.imagenes)
    } catch {
      imagenesArr = []
    }
  } else if (service.SER_IMAGENES && typeof service.SER_IMAGENES === 'string' && service.SER_IMAGENES.trim().startsWith('[')) {
    try {
      imagenesArr = JSON.parse(service.SER_IMAGENES)
    } catch {
      imagenesArr = []
    }
  }

  emit('edit', {
    id: service.id ?? service.SER_ID,
    titulo: service.title ?? service.SER_TITULO,
    descripcion_corta: service.descripcion_corta ?? service.SER_DESCRIPCION_CORTA ?? service.desc ?? '',
    descripcion_larga: service.descripcion_larga ?? service.SER_DESCRIPCION_LARGA ?? service.description ?? '',
    incluye: incluyeArr,
    precio: parsePrice(service.price ?? service.SER_PRECIO),
    icono: service.icon ?? service.SER_ICONO,
    estado: service.status ?? service.SER_ESTADO,
    available: service.available,
    imagenes: imagenesArr // <-- AÑADIDO
  })
}
</script>

<style scoped>
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
.admin-card {
  background: white;
  border-radius: 0.5rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  transition: all 0.3s ease;
}
.admin-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}
.admin-card-header {
  background: linear-gradient(90deg, #BCAA81, #C18F67);
  color: white;
  font-family: 'Playfair Display', serif;
  padding: 1rem 1.5rem;
}
</style>
<template>
  <div class="admin-card rounded-lg overflow-hidden">
    <ServiceFormHeader :isEditMode="isEditMode" />
    <div class="p-6">
      <form @submit.prevent="onSubmit">
        <ServiceFormFields
          v-model:title="title"
          v-model:descripcion_corta="descripcion_corta"
          v-model:descripcion_larga="descripcion_larga"
          v-model:price="price"
          v-model:icon="icon"
          v-model:estadoActivo="estadoActivo"
          :iconOptions="iconOptions"
        />
        <ServiceIncludes
          v-model:incluye="incluye"
        />
        <ServiceImages
          v-model:imagenes="imagenes"
          @eliminarImagen="eliminarImagen"
          @handleGestorArchivos="handleGestorArchivos"
        />
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

import ServiceFormHeader from './ServiceFormHeader.vue'
import ServiceFormFields from './ServiceFormFields.vue'
import ServiceIncludes from './ServiceIncludes.vue'
import ServiceImages from './ServiceImages.vue'

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
const price = ref(0)
const icon = ref('fa-camera-retro')
const estadoActivo = ref(false)
const estado = ref('Oculto')
const imagenes = ref([])

watch(estadoActivo, (val) => {
  estado.value = val ? 'Activo' : 'Oculto'
})

watch(imagenes, (val) => {
  console.log('[CreateServices.vue] watcher imagenes:', [...val])
})

function normalizarImagenes(arr) {
  // Convierte un array de strings o de objetos a objetos {url, name, extension}
  if (!Array.isArray(arr)) return []
  return arr.map(img => {
    if (typeof img === 'string') {
      const partes = img.split('/')
      const nombreCompleto = partes[partes.length - 1] || ''
      const extArr = nombreCompleto.split('.')
      const extension = extArr.length > 1 ? extArr.pop() : ''
      const name = extArr.join('.')
      return { url: img, name, extension }
    } else if (img && typeof img === 'object' && img.url) {
      return {
        url: img.url,
        name: img.name || '',
        extension: img.extension || ''
      }
    }
    return { url: '', name: '', extension: '' }
  })
}

const lastEditId = ref(null)

watch(
  () => props.serviceToEdit,
  (val) => {
    // Si no hay servicio a editar, limpiar todo y resetear lastEditId
    if (!val) {
      lastEditId.value = null
      title.value = ''
      descripcion_corta.value = ''
      descripcion_larga.value = ''
      incluye.value = []
      price.value = 0
      icon.value = 'fa-camera-retro'
      estadoActivo.value = false
      estado.value = 'Oculto'
      imagenes.value = []
      console.log('[Editar] No hay servicio a editar, limpiando campos.')
      console.log('[Editar] imagenes.value:', [...imagenes.value]);
      return
    }
    // Solo actualiza si cambia el id del servicio a editar
    const newId = val?.id ?? val?.SER_ID ?? null
    if (newId !== lastEditId.value) {
      lastEditId.value = newId
      title.value = val.titulo ?? val.title ?? val.SER_TITULO ?? ''
      descripcion_corta.value = val.descripcion_corta ?? val.short_description ?? val.SER_DESCRIPCION_CORTA ?? ''
      descripcion_larga.value = val.descripcion_larga ?? val.long_description ?? val.SER_DESCRIPCION_LARGA ?? ''
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
      console.log('[Editar] Array incluye al editar:', incluye.value)
      // Precio
      price.value = val.precio ?? val.price ?? val.SER_PRECIO ?? 0
      // Icono
      icon.value = val.icono || val.icon || val.SER_ICONO || 'fa-camera-retro'
      // Estado
      estadoActivo.value = (val.estado ?? val.status ?? val.SER_ESTADO) === 'Activo'
      estado.value = val.estado ?? val.status ?? val.SER_ESTADO ?? 'Oculto'
      // Imágenes (array o string JSON)
      let imgs = []
      if (Array.isArray(val.imagenes)) {
        imgs = val.imagenes
        console.log('[Editar] imagenes es array:', Array.from(imgs))
      } else if (typeof val.imagenes === 'string' && val.imagenes.trim().startsWith('[')) {
        try {
          imgs = JSON.parse(val.imagenes)
          console.log('[Editar] imagenes es string JSON:', Array.from(imgs))
        } catch {
          imgs = []
          console.log('[Editar] imagenes string JSON inválido')
        }
      } else if (val.SER_IMAGENES && typeof val.SER_IMAGENES === 'string' && val.SER_IMAGENES.trim().startsWith('[')) {
        try {
          imgs = JSON.parse(val.SER_IMAGENES)
          console.log('[Editar] SER_IMAGENES es string JSON:', Array.from(imgs))
        } catch {
          imgs = []
          console.log('[Editar] SER_IMAGENES string JSON inválido')
        }
      } else {
        imgs = []
        console.log('[Editar] No hay imágenes en el servicio')
      }
      imagenes.value = normalizarImagenes(imgs)
      console.log('[Editar] Array imagenes normalizado:', [...imagenes.value])
    }
  },
  { immediate: true }
)

function agregarIncluye() {
  if (nuevoIncluye.value.trim() !== '') {
    incluye.value.push(nuevoIncluye.value.trim())
    nuevoIncluye.value = ''
  }
}
function eliminarIncluye(idx) {
  incluye.value.splice(idx, 1)
}

function agregarImagen() {
  if (nuevaImagen.value.trim() && imagenes.value.length < 12) {
    imagenes.value.push(nuevaImagen.value.trim())
    nuevaImagen.value = ''
  }
}
function eliminarImagen(idx) {
  // Si es preview local, libera memoria
  if (imagenes.value[idx].preview) {
    URL.revokeObjectURL(imagenes.value[idx].preview)
  }
  imagenes.value.splice(idx, 1)
}

function handleGestorArchivos(files) {
  // Normaliza el array de archivos/imágenes para mantener el mismo formato
  console.log('[GestorArchivos] Archivos recibidos:', Array.from(files))
  imagenes.value = files.map(img => {
    if (img.file) {
      // Imagen nueva subida
      return {
        file: img.file,
        name: img.name,
        extension: img.extension,
        preview: img.preview
      }
    } else if (img.url) {
      // Imagen ya existente
      return {
        url: img.url,
        name: img.name,
        extension: img.extension
      }
    }
    return img
  })
  console.log('[GestorArchivos] Array imagenes tras normalizar:', [...imagenes.value])
  if (!imagenes.value.length) {
    console.warn('[GestorArchivos] El array de imagenes está vacío tras normalizar.')
  }
}

async function subirImagen(file) {
  const formData = new FormData()
  formData.append('imagen', file)
  const res = await fetch('http://localhost:8080/Apis/API_subir_imagen.php', {
    method: 'POST',
    body: formData
  })
  const data = await res.json()
  if (data.success && data.url) {
    return data.url
  }
  return null
}

async function onSubmit() {
  // Subir imágenes nuevas primero y obtener sus URLs
  let urls = []
  for (const img of imagenes.value) {
    if (img.url) {
      urls.push(img.url) // Ya subida (caso edición)
    } else if (img.file) {
      const url = await subirImagen(img.file)
      if (url) urls.push(url)
    }
  }
  if (isEditMode.value) {
    await updateService(urls)
  } else {
    await createService(urls)
  }
}

async function createService(urls) {
  const body = {
    icono: icon.value,
    precio: price.value,
    titulo: title.value,
    descripcion_corta: descripcion_corta.value,
    descripcion_larga: descripcion_larga.value,
    incluye: JSON.stringify(incluye.value),
    estado: estado.value,
    imagenes: JSON.stringify(urls)
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
      imagenes.value = []
      window.location.reload()
    } else {
      alert('Error al crear servicio: ' + data.error)
    }
  } catch (e) {
    alert('Error de red')
  }
}

async function updateService(urls) {
  // Detecta si hay imágenes nuevas (tipo File)
  const tieneNuevas = imagenes.value.some(img => img.file)
  if (tieneNuevas) {
    // Usar FormData si hay imágenes nuevas
    const formData = new FormData()
    formData.append('id', props.serviceToEdit.id || props.serviceToEdit.SER_ID)
    formData.append('icono', icon.value)
    formData.append('precio', price.value)
    formData.append('titulo', title.value)
    formData.append('descripcion_corta', descripcion_corta.value)
    formData.append('descripcion_larga', descripcion_larga.value)
    formData.append('incluye', JSON.stringify(incluye.value))
    formData.append('estado', estado.value)
    // Añade solo las imágenes nuevas
    imagenes.value.forEach(img => {
      if (img.file) {
        formData.append('imagenes[]', img.file)
      }
    })
    // Añade las URLs de las imágenes ya existentes (las que no son nuevas)
    const existentes = imagenes.value.filter(img => img.url && !img.file).map(img => img.url)
    formData.append('imagenes_existentes', JSON.stringify(existentes))

    try {
      const res = await fetch('http://localhost:8080/Apis/API_editar_servicio.php', {
        method: 'POST',
        body: formData
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
        imagenes.value = []
        window.location.reload()
      } else {
        alert('Error al actualizar servicio: ' + data.error)
      }
    } catch (e) {
      alert('Error de red')
    }
  } else {
    // Si no hay imágenes nuevas, usa JSON normal
    const body = {
      id: props.serviceToEdit.id || props.serviceToEdit.SER_ID,
      icono: icon.value,
      precio: price.value,
      titulo: title.value,
      descripcion_corta: descripcion_corta.value,
      descripcion_larga: descripcion_larga.value,
      incluye: JSON.stringify(incluye.value),
      estado: estado.value,
      imagenes: JSON.stringify(urls)
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
        imagenes.value = []
        window.location.reload()
      } else {
        alert('Error al actualizar servicio: ' + data.error)
      }
    } catch (e) {
      alert('Error de red')
    }
  }
}
</script>
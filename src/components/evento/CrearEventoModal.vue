<template>
  <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md relative">
      <button
        class="absolute top-2 right-2 text-gray-500 hover:text-gray-700"
        @click="$emit('close')"
        aria-label="Cerrar"
      >
        ✕
      </button>
      <h2 class="text-2xl font-bold mb-4">Crear nuevo evento</h2>
      <form @submit.prevent="handleSubmit" class="space-y-4">
        <!-- titulo -->
        <div>
          <label class="block text-sm font-medium mb-1">Título</label>
          <input v-model="form.titulo" type="text" maxlength="150" class="input" required />
        </div>
        <!-- descripcion -->
        <div>
          <label class="block text-sm font-medium mb-1">Descripción</label>
          <textarea v-model="form.descripcion" class="input" rows="3"></textarea>
        </div>
        <!-- fecha_evento -->
        <div>
          <label class="block text-sm font-medium mb-1">Fecha y hora del evento</label>
          <input v-model="form.fecha_evento" type="datetime-local" class="input" required />
        </div>
        <!-- ciudad -->
        <div class="relative">
          <label class="block text-sm font-medium mb-1">Ciudad</label>
          <input
            v-model="form.ciudad"
            type="text"
            maxlength="100"
            class="input"
            required
            autocomplete="off"
            @input="onCiudadInput"
            @focus="onCiudadFocus"
            @blur="onCiudadBlur"
          />
          <ul
            v-if="showCiudadDropdown && ciudadSugerencias.length"
            class="absolute z-10 bg-white border w-full mt-1 rounded shadow max-h-40 overflow-auto"
            style="background: white; color: black; border: 1px solid #ccc;"
          >
            <li
              v-for="(ciudad, idx) in ciudadSugerencias"
              :key="ciudad + '-' + idx"
              @mousedown.prevent="seleccionarCiudad(ciudad)"
              class="px-3 py-2 hover:bg-blue-100 cursor-pointer"
              v-html="highlightMatch(ciudad, form.ciudad)"
              style="background: white; color: black;"
            ></li>
          </ul>
          <div v-if="ciudadError" class="text-red-600 text-sm mt-1">{{ ciudadError }}</div>
        </div>
        <!-- calle -->
        <div>
          <label class="block text-sm font-medium mb-1">Calle</label>
          <input
            v-model="form.calle"
            type="text"
            maxlength="150"
            class="input"
            required
            :disabled="!form.ciudad"
            autocomplete="off"
          />
          <!-- TODO: Integrar autocompletado de calles con Nominatim según ciudad seleccionada -->
        </div>
        <!-- capacidad_maxima -->
        <div>
          <label class="block text-sm font-medium mb-1">Capacidad máxima</label>
          <input v-model="form.capacidad_maxima" type="number" min="1" class="input" />
        </div>
        <!-- visibilidad -->
        <div>
          <label class="block text-sm font-medium mb-1">Visibilidad</label>
          <select v-model="form.visibilidad" class="input">
            <option value="Público">Público</option>
            <option value="Privado">Privado</option>
          </select>
        </div>
        <div class="flex justify-end">
          <button
            type="submit"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
          >
            {{ modoEdicion ? 'Editar' : 'Crear' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onUnmounted, watch, toRefs } from 'vue'
import ciudadesJson from './pruebaciudades.json'

const props = defineProps({
  eventoAEditar: { type: Object, default: null },
  modoEdicion: { type: Boolean, default: false }
})

const emit = defineEmits(['close', 'evento-editado'])

const form = reactive({
  titulo: '',
  descripcion: '',
  fecha_evento: '',
  ciudad: '',
  calle: '',
  capacidad_maxima: '',
  visibilidad: 'Público'
})

// Rellenar el formulario si es edición
watch(
  () => props.eventoAEditar,
  (nuevo) => {
    if (nuevo) {
      form.titulo = nuevo.titulo || ''
      form.descripcion = nuevo.descripcion || ''
      form.fecha_evento = nuevo.fecha_evento || ''
      form.ciudad = nuevo.ciudad || ''
      form.calle = nuevo.calle || ''
      form.capacidad_maxima = nuevo.capacidad_maxima || ''
      form.visibilidad = nuevo.visibilidad || 'Público'
    } else {
      form.titulo = ''
      form.descripcion = ''
      form.fecha_evento = ''
      form.ciudad = ''
      form.calle = ''
      form.capacidad_maxima = ''
      form.visibilidad = 'Público'
    }
  },
  { immediate: true }
)

const ciudadSugerencias = ref([])
const showCiudadDropdown = ref(false)
const ciudadError = ref('')

const blurTimeout = ref(null)

// Extraer todas las ciudades del JSON anidado
function extraerCiudades(data) {
  const resultado = []
  for (const comunidad of data) {
    if (!comunidad.provinces) continue
    for (const provincia of comunidad.provinces) {
      if (!provincia.towns) continue
      for (const town of provincia.towns) {
        if (town.label) resultado.push(town.label)
      }
    }
  }
  return resultado
}

const ciudades = extraerCiudades(ciudadesJson)

function highlightMatch(text, query) {
  if (!query) return text
  const regex = new RegExp(`(${query})`, 'ig')
  return text.replace(regex, '<b>$1</b>')
}

function onCiudadInput(e) {
  const value = e.target.value.trim()
  form.ciudad = value
  ciudadError.value = ''
  if (!value) {
    ciudadSugerencias.value = []
    showCiudadDropdown.value = false
    return
  }
  ciudadSugerencias.value = ciudades.filter(ciudad =>
    ciudad.toLowerCase().includes(value.toLowerCase())
  )
  showCiudadDropdown.value = ciudadSugerencias.value.length > 0
}

function onCiudadFocus() {
  if (form.ciudad) {
    onCiudadInput({ target: { value: form.ciudad } })
  } else {
    ciudadSugerencias.value = ciudades.slice(0, 10)
    showCiudadDropdown.value = true
  }
}

function onCiudadBlur() {
  blurTimeout.value = setTimeout(() => {
    showCiudadDropdown.value = false
  }, 100)
}

onUnmounted(() => {
  if (blurTimeout.value) {
    clearTimeout(blurTimeout.value)
    blurTimeout.value = null
  }
})

function seleccionarCiudad(ciudad) {
  form.ciudad = ciudad
  ciudadSugerencias.value = []
  showCiudadDropdown.value = false
  ciudadError.value = ''
}

async function handleSubmit() {
  // Validar que la ciudad esté entre las sugerencias actuales
  const ciudadValida = ciudades.some(
    c => c.toLowerCase() === form.ciudad.trim().toLowerCase()
  )
  if (!ciudadValida) {
    ciudadError.value = 'Selecciona una ciudad válida de la lista.'
    showCiudadDropdown.value = true
    return
  }
  ciudadError.value = ''

  // Llamada a la API para crear el evento con la URL correcta
  try {
    if (props.modoEdicion && props.eventoAEditar) {
      // Lógica para editar evento
      // await fetch('URL_EDITAR_EVENTO', { method: 'POST', body: ... })
      emit('evento-editado', { ...form, id: props.eventoAEditar.id })
      emit('close')
    } else {
      // Lógica para crear evento
      const response = await fetch('http://localhost:8080/Apis/API_crear_evento.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(form)
      })
      if (!response.ok) {
        throw new Error('Error al crear el evento')
      }
      emit('close')
    }
  } catch (err) {
    ciudadError.value = 'No se pudo crear el evento. Intenta de nuevo.'
  }
}
</script>

<style scoped>

</style>
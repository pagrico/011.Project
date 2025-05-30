<template>
  <div
    class="drop-area"
    @dragover.prevent="onDragOver"
    @dragleave.prevent="onDragLeave"
    @drop.prevent="onDrop"
    :class="{ 'is-dragover': isDragOver }"
    @click="triggerFileInput"
  >
    <div class="icon-container">
      <svg width="48" height="48" viewBox="0 0 48 48" fill="none">
        <circle cx="24" cy="24" r="18" fill="#BCAA81"/>
        <path d="M24 16v14M24 16l-6 6M24 16l6 6" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </div>
    <div class="drop-text">
      <strong>Arrastra imágenes aquí o haz clic para seleccionar</strong>
      <div class="text-xs text-[#BCAA81] mt-1">Formatos permitidos: jpg, jpeg, png, gif, webp</div>
    </div>
    <input
      type="file"
      ref="fileInput"
      class="file-input"
      accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
      @change="onFileChange"
    />
  </div>
  <div v-if="imagenes.length" class="imagenes-list mt-4">
    <div
      v-for="(img, idx) in imagenes"
      :key="idx"
      class="imagen-item flex items-center mb-2 bg-[#f8f6f1] rounded p-2 shadow-sm"
    >
      <img :src="img.preview || img.url" alt="preview" class="w-12 h-12 object-cover rounded border border-[#BCAA81] mr-3" />
      <div class="flex-1">
        <div class="font-medium text-[#BCAA81] truncate">{{ img.name }}</div>
        <div class="text-xs text-gray-500">{{ img.extension }}</div>
      </div>
      <button type="button" @click="eliminarImagen(idx)" class="ml-2 text-red-500 hover:text-red-700 px-2 py-1 rounded transition">
        <i class="fas fa-times"></i>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

// Permite recibir imágenes iniciales
const props = defineProps({
  modelValue: { type: Array, default: undefined },
  images: { type: Array, default: undefined }
})

const emit = defineEmits(['change'])
const isDragOver = ref(false)
const fileInput = ref(null)
const imagenes = ref([]) // [{file, name, extension, preview, url?}]

// Inicializa con imágenes existentes si se pasan por props
watch(
  () => props.images,
  (val) => {
    if (Array.isArray(val)) {
      imagenes.value = val.map(img => {
        if (typeof img === 'string') {
          const partes = img.split('/')
          const nombreCompleto = partes[partes.length - 1] || ''
          const extArr = nombreCompleto.split('.')
          const extension = extArr.length > 1 ? extArr.pop() : ''
          const name = extArr.join('.')
          return { url: img, name, extension }
        } else if (img && typeof img === 'object' && (img.url || img.file)) {
          // Si es objeto con url o file, lo dejamos igual
          return { ...img }
        }
        return { url: '', name: '', extension: '' }
      })
      emitChange()
    }
  },
  { immediate: true }
)

function onDragOver() {
  isDragOver.value = true
}
function onDragLeave() {
  isDragOver.value = false
}
function onDrop(event) {
  isDragOver.value = false
  const files = event.dataTransfer.files
  handleFiles(files)
}
function onFileChange(event) {
  const files = event.target.files
  handleFiles(files)
}
function triggerFileInput() {
  fileInput.value && fileInput.value.click()
}
function handleFiles(files) {
  // Solo permite añadir una imagen por vez (la primera)
  const fileArr = Array.from(files)
  if (fileArr.length === 0) return
  const file = fileArr[0]
  if (!file.type.startsWith('image/')) return
  const extension = file.name.split('.').pop()
  const preview = URL.createObjectURL(file)
  imagenes.value.push({
    file,
    name: file.name,
    extension,
    preview
  })
  // Log aquí tras importar
  console.log('[GestorArchuivos] Imagen importada:', imagenes.value)
  emitChange()
}
function eliminarImagen(idx) {
  // Liberar memoria del preview
  if (imagenes.value[idx].preview) {
    URL.revokeObjectURL(imagenes.value[idx].preview)
  }
  imagenes.value.splice(idx, 1)
  emitChange()
}
function emitChange() {
  // Devuelve los objetos con file, name, extension, preview, url
  emit('change', imagenes.value)
}
</script>

<style scoped>
.drop-area {
  background: #fff;
  border: 2px dashed #BCAA81;
  border-radius: 10px;
  padding: 40px 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 180px;
  transition: border-color 0.2s, background 0.2s;
  cursor: pointer;
  width: 100%;
  box-sizing: border-box;
  position: relative;
}
.drop-area.is-dragover {
  border-color: #a18c4a;
  background: #f8f6f1;
}
.icon-container {
  margin-bottom: 12px;
}
.drop-text {
  color: #222;
  font-size: 1.1rem;
  font-weight: 500;
  text-align: center;
}
.file-input {
  display: none;
}
.imagenes-list {
  margin-top: 1rem;
}
.imagen-item {
  transition: background 0.2s;
}
.imagen-item:hover {
  background: #f3ede0;
}
</style>
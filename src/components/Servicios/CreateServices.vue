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
          <button type="submit" class="btn-primary px-6 py-2 rounded-md font-medium" :disabled="isSubmitting">
            {{ isEditMode ? (isSubmitting ? 'Actualizando...' : 'Actualizar') : (isSubmitting ? 'Creando...' : 'Crear servicio') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed, inject } from 'vue'
const emit = defineEmits(['created', 'updated'])
const props = defineProps({
  serviceToEdit: {
    type: Object,
    default: null
  }
})

// Assuming these are local components; adjust path if necessary
import ServiceFormHeader from './ServiceFormHeader.vue'
import ServiceFormFields from './ServiceFormFields.vue'
import ServiceIncludes from './ServiceIncludes.vue'
import ServiceImages from './ServiceImages.vue'

const iconOptions = [
  { value: 'fa-camera-retro', label: 'Fotografía' },
  { value: 'fa-video', label: 'Video' },
  { value: 'fa-paint-brush', label: 'Diseño gráfico' },
  { value: 'fa-laptop-code', label: 'Desarrollo web' },
  // ... (keep all your icon options)
  { value: 'fa-file-invoice-dollar', label: 'Facturación' }
]

const isEditMode = computed(() => !!props.serviceToEdit)

const title = ref('')
const descripcion_corta = ref('')
const descripcion_larga = ref('')
const incluye = ref([])
const price = ref(0)
const icon = ref('fa-camera-retro')
const estadoActivo = ref(false) // For the checkbox
const estado = ref('Oculto') // Actual state string for API
const imagenes = ref([]) // Holds { file: File, name: string, preview: string } or { url: string, name: string, extension: string }

const lastEditId = ref(null)
const isSubmitting = ref(false)

// Inyectar la función de alertas desde App.vue
const showAlert = inject('showAlert')

watch(
  () => props.serviceToEdit,
  (val) => {
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
      return
    }

    const safeVal = JSON.parse(JSON.stringify(val))
    const newId = safeVal?.id ?? safeVal?.SER_ID ?? null

    if (newId && newId !== lastEditId.value) {
      lastEditId.value = newId
      title.value = safeVal.titulo ?? safeVal.title ?? safeVal.SER_TITULO ?? ''
      descripcion_corta.value = safeVal.descripcion_corta ?? safeVal.short_description ?? safeVal.SER_DESCRIPCION_CORTA ?? ''
      descripcion_larga.value = safeVal.descripcion_larga ?? safeVal.long_description ?? safeVal.SER_DESCRIPCION_LARGA ?? ''
      
      let newIncluye = []
      if (Array.isArray(safeVal.incluye)) newIncluye = [...safeVal.incluye]
      else if (Array.isArray(safeVal.package)) newIncluye = [...safeVal.package]
      else if (typeof safeVal.incluye === 'string' && safeVal.incluye.trim().startsWith('[')) {
        try { newIncluye = JSON.parse(safeVal.incluye) } catch { newIncluye = [] }
      }
      incluye.value = newIncluye

      price.value = safeVal.precio ?? safeVal.price ?? safeVal.SER_PRECIO ?? 0
      icon.value = safeVal.icono || safeVal.icon || safeVal.SER_ICONO || 'fa-camera-retro'
      
      const newEstado = safeVal.estado ?? safeVal.status ?? safeVal.SER_ESTADO ?? 'Oculto'
      estado.value = newEstado
      estadoActivo.value = newEstado === 'Activo'

      let imgsData = []
      if (Array.isArray(safeVal.imagenes)) imgsData = safeVal.imagenes
      else if (typeof safeVal.imagenes === 'string' && safeVal.imagenes.trim().startsWith('[')) {
        try { imgsData = JSON.parse(safeVal.imagenes) } catch { imgsData = [] }
      } else if (safeVal.SER_IMAGENES && typeof safeVal.SER_IMAGENES === 'string' && safeVal.SER_IMAGENES.trim().startsWith('[')) {
        try { imgsData = JSON.parse(safeVal.SER_IMAGENES) } catch { imgsData = [] }
      }
      imagenes.value = normalizarImagenes(imgsData)
    } else if (!newId && lastEditId.value !== null) { // ServiceToEdit becomes null (e.g. cancelling edit)
        // Reset form if we were editing and now serviceToEdit is null
        lastEditId.value = null;
        title.value = '';
        descripcion_corta.value = '';
        descripcion_larga.value = '';
        incluye.value = [];
        price.value = 0;
        icon.value = 'fa-camera-retro';
        estadoActivo.value = false;
        estado.value = 'Oculto';
        imagenes.value = [];
    }
  },
  { immediate: true, deep: true } // deep true might be intensive, consider if only ID change matters
)

function eliminarImagen(idx) {
  if (imagenes.value[idx] && imagenes.value[idx].preview) {
    URL.revokeObjectURL(imagenes.value[idx].preview)
  }
  imagenes.value.splice(idx, 1)
}

function handleGestorArchivos(filesFromGestor) {
  console.log("Archivos recibidos del gestor:", filesFromGestor);
  const newImagenes = filesFromGestor.map(img => {
    if (img.file instanceof File) { // Check if it's a new file object
      return {
        file: img.file,
        name: img.name || img.file.name,
        extension: img.extension || img.file.name.split('.').pop(),
        preview: img.preview || URL.createObjectURL(img.file) // Create preview if not exists
      }
    } else if (img.url) { // Existing image
      return {
        url: img.url,
        name: img.name || img.url.split('/').pop().split('.')[0],
        extension: img.extension || img.url.split('.').pop()
      }
    }
    return img; // Fallback, though should be one of the above
  });
  
  // Debug log para verificar imágenes procesadas
  console.log("Imágenes procesadas:", newImagenes);
  
  imagenes.value = newImagenes;
}


function normalizarImagenes(arr) {
  if (!Array.isArray(arr)) return []
  return arr.map(img => {
    if (typeof img === 'string') { // If it's just a URL string
      const nombreCompleto = img.split('/').pop() || ''
      const parts = nombreCompleto.split('.')
      const extension = parts.length > 1 ? parts.pop() : ''
      const name = parts.join('.')
      return { url: img, name, extension }
    } else if (img && typeof img === 'object') { // If it's an object (potentially from server or new upload)
      if (img.file instanceof File) { // Already processed new file
        return img;
      }
      return {
        url: img.url || '',
        name: img.name || (img.url ? img.url.split('/').pop().split('.')[0] : ''),
        extension: img.extension || (img.url ? img.url.split('.').pop() : '')
      }
    }
    return { url: '', name: '', extension: '' } // Fallback for malformed entries
  })
}

async function onSubmit() {
  if (isSubmitting.value) return
  isSubmitting.value = true

  // Note: !price.value will be true if price is 0. If 0 is a valid price, adjust this validation.
  if (!title.value.trim() || !descripcion_corta.value.trim() || !descripcion_larga.value.trim() || !price.value) {
    showAlert('Por favor, completa todos los campos obligatorios (Título, descripciones, precio).', false);
    isSubmitting.value = false
    return
  }

  try {
    if (isEditMode.value) {
      await updateService()
    } else {
      await createService()
    }
  } catch (error) {
    // This catch is for unexpected errors in updateService/createService themselves,
    // not for API call errors which are handled within those functions.
    console.error("Error in onSubmit:", error);
    showAlert('Se produjo un error inesperado al procesar el formulario.', false, 'Error Inesperado');
  } finally {
    isSubmitting.value = false
  }
}

function clearForm() {
  title.value = ''
  descripcion_corta.value = ''
  descripcion_larga.value = ''
  incluye.value = []
  price.value = 0
  icon.value = 'fa-camera-retro'
  estadoActivo.value = false // This will trigger the watch to set estado.value = 'Oculto'
  imagenes.value.forEach(img => { // Revoke any local object URLs
    if (img.preview) URL.revokeObjectURL(img.preview);
  });
  imagenes.value = []
  // lastEditId.value should be reset by the parent passing null to serviceToEdit or by watch logic.
}

async function commonAPICall(url, method, body) {
  try {
    // Si body es FormData, no establecer Content-Type para que el navegador lo configure automáticamente
    const headers = {};
    if (!(body instanceof FormData)) {
      headers['Content-Type'] = 'application/json';
    }

    const options = {
      method,
      headers,
      body
    };

    // Añadir un timeout por si el servidor no responde
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 segundos
    options.signal = controller.signal;

    const response = await fetch(url, options);
    clearTimeout(timeoutId);

    console.log(`[commonAPICall] Response from ${url}:`, {
      ok: response.ok,
      status: response.status,
      statusText: response.statusText,
      headers: Object.fromEntries(response.headers.entries())
    });

    if (!response.ok) {
      let errorData;
      let errorTextForNonJson = '';
      try {
        // Intentar clonar la respuesta para leerla como texto y luego como JSON
        const clonedResponse = response.clone();
        errorTextForNonJson = await clonedResponse.text();
        try {
          errorData = await response.json();
          console.log(`[commonAPICall] Error JSON from ${url}:`, errorData);
        } catch {
          // Si no es JSON, usar el texto de error
          throw new Error(`El servidor respondió con estado ${response.status}. ${errorTextForNonJson || response.statusText}`);
        }
      } catch (e) {
        console.error(`[commonAPICall] Failed to parse error as JSON from ${url}. Text response:`, errorTextForNonJson, e);
        throw new Error(`El servidor respondió con estado ${response.status}. ${errorTextForNonJson || response.statusText}`);
      }
      // Si es JSON, errorData.error podría contener un mensaje específico de tu API
      throw new Error(errorData.error || `El servidor respondió con estado ${response.status}.`);
    }

    // Si la respuesta es OK, intentar leerla como JSON
    const responseText = await response.text();
    console.log(`[commonAPICall] Response text from ${url}:`, responseText);
    try {
      return responseText ? JSON.parse(responseText) : {};
    } catch (e) {
      console.error(`[commonAPICall] Failed to parse successful response as JSON from ${url}. Text was:`, responseText, e);
      throw new Error('La respuesta del servidor no es JSON válido, aunque el estado fue OK.');
    }

  } catch (error) {
    console.error(`[commonAPICall] API call to ${url} failed:`, error);
    
    // Mensaje más descriptivo según el tipo de error
    let errorMessage = 'Error de comunicación con el servidor.';
    if (error.name === 'AbortError') {
      errorMessage = 'La solicitud tardó demasiado en completarse. Por favor, inténtelo de nuevo.';
    } else if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
      errorMessage = 'No se pudo conectar con el servidor. Por favor, verifique su conexión a Internet.';
    } else if (error instanceof Error) {
      errorMessage = error.message;
    }
    
    throw new Error(errorMessage);
  }
}


async function createService() {
  const formData = new FormData();
  formData.append('icono', icon.value);
  formData.append('precio', price.value.toString());
  formData.append('titulo', title.value);
  formData.append('descripcion_corta', descripcion_corta.value);
  formData.append('descripcion_larga', descripcion_larga.value);
  formData.append('incluye', JSON.stringify(incluye.value));
  formData.append('estado', estado.value);

  let hasNewImages = false;
  let imageCounter = 0;
  
  // Procesamiento mejorado de imágenes para crear servicio
  console.log("Creando servicio con imágenes:", imagenes.value);
  
  for (const img of imagenes.value) {
    if (img.file instanceof File) { // Nueva imagen (objeto File)
      console.log(`Anexando imagen nueva ${imageCounter}:`, img.name);
      // Importante: usar un nombre único para cada entrada de archivo
      formData.append(`imagenesNuevas_${imageCounter}`, img.file, img.name);
      hasNewImages = true;
      imageCounter++;
    }
  }
  
  // Almacenar el contador total de imágenes nuevas
  formData.append('imagenesNuevasCount', imageCounter.toString());

  try {
    // Usar XMLHttpRequest para mayor control sobre la subida de archivos
    if (hasNewImages) {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'http://localhost:8080/Apis/API_crear_servicio.php', true);
        
        xhr.upload.addEventListener('progress', (event) => {
          if (event.lengthComputable) {
            const percentComplete = (event.loaded / event.total) * 100;
            console.log(`Progreso de subida: ${percentComplete.toFixed(2)}%`);
          }
        });
        
        xhr.onload = function() {
          if (xhr.status === 200 || xhr.status === 201) {
            console.log("Respuesta del servidor:", xhr.responseText);
            try {
              const response = JSON.parse(xhr.responseText);
              resolve(response);
            } catch (e) {
              console.error("Error al parsear respuesta:", e, xhr.responseText);
              reject(new Error('Respuesta no válida del servidor: ' + xhr.responseText));
            }
          } else {
            console.error("Error del servidor:", xhr.status, xhr.statusText, xhr.responseText);
            reject(new Error(`Error en la respuesta del servidor: ${xhr.status} ${xhr.statusText} - ${xhr.responseText}`));
          }
        };
        
        xhr.onerror = function(e) {
          console.error("Error de conexión:", e);
          reject(new Error('Error de conexión al servidor.'));
        };
        
        xhr.send(formData);
      }).then(data => {
        if (data.success) {
          emit('created', data.service);
          clearForm();
          showAlert('El servicio se ha creado correctamente.', true);
          setTimeout(() => {
            location.reload();
          }, 1500);
        } else {
          showAlert(`Error al crear servicio: ${data.error || 'Error desconocido.'}`, false);
        }
      }).catch(e => {
        showAlert(`Error al crear servicio: ${e.message}`, false);
      });
    } else {
      // Si no hay imágenes, usar commonAPICall
      const data = await commonAPICall('http://localhost:8080/Apis/API_crear_servicio.php', 'POST', formData);

      if (data.success) {
        emit('created', data.service);
        clearForm();
        showAlert('El servicio se ha creado correctamente.', true);
        setTimeout(() => {
          location.reload();
        }, 1500);
      } else {
        showAlert(`Error al crear servicio: ${data.error || 'Error desconocido.'}`, false);
      }
    }
  } catch (e) {
    showAlert(`Error al crear servicio: ${e.message}`, false);
  }
}

async function updateService() {
  try {
    const serviceId = props.serviceToEdit?.id ?? props.serviceToEdit?.SER_ID;
    if (!serviceId) {
      showAlert('Error: No se pudo identificar el servicio a actualizar.', false);
      isSubmitting.value = false;
      return;
    }

    const formData = new FormData();
    formData.append('id', serviceId.toString()); // Asegurarse de que el ID se convierte a string
    console.log(`ID del servicio a actualizar: ${serviceId}`); // Depuración
    
    formData.append('icono', icon.value);
    formData.append('precio', price.value.toString());
    formData.append('titulo', title.value);
    formData.append('descripcion_corta', descripcion_corta.value);
    formData.append('descripcion_larga', descripcion_larga.value);
    formData.append('incluye', JSON.stringify(incluye.value));
    formData.append('estado', estado.value);

    const imagenesExistentesUrls = [];
    let hasNewImages = false;

    // Depurar para ver qué imágenes se están procesando
    console.log("Total de imágenes a procesar:", imagenes.value.length);
    
    // Primero registramos todas las URLs de imágenes existentes
    imagenes.value.forEach((img, index) => {
      if (img.url) {
        imagenesExistentesUrls.push(img.url);
        console.log(`Imagen existente ${index}:`, img.url);
      }
    });
    
    // Ahora procesamos los nuevos archivos de imagen
    let imageCounter = 0;
    for (const img of imagenes.value) {
      if (img.file instanceof File) {
        console.log(`Anexando imagen nueva ${imageCounter}:`, img.name);
        formData.append(`imagenesNuevas_${imageCounter}`, img.file, img.name);
        hasNewImages = true;
        imageCounter++;
      }
    }
    
    // Para depuración, imprimir los datos del FormData
    console.log("[updateService] Datos a enviar:");
    for (let [key, value] of formData.entries()) {
      if (!key.startsWith('imagenesNuevas_')) {
        console.log(`${key}: ${value}`);
      } else {
        console.log(`${key}: [Archivo]`);
      }
    }
    
    formData.append('imagenesNuevasCount', imageCounter.toString());
    formData.append('imagenesExistentes', JSON.stringify(imagenesExistentesUrls));

    // Usar XMLHttpRequest modificado para mejor manejo de archivos múltiples
    if (hasNewImages) {
      return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'http://localhost:8080/Apis/API_editar_servicio.php', true);
        
        // Añadir listeners para seguir el progreso de la subida
        xhr.upload.addEventListener('progress', (event) => {
          if (event.lengthComputable) {
            const percentComplete = (event.loaded / event.total) * 100;
            console.log(`Progreso de subida: ${percentComplete.toFixed(2)}%`);
          }
        });
        
        xhr.onload = function() {
          if (xhr.status === 200) {
            console.log("Respuesta del servidor:", xhr.responseText);
            try {
              const response = JSON.parse(xhr.responseText);
              resolve(response);
            } catch (e) {
              console.error("Error al parsear respuesta:", e, xhr.responseText);
              reject(new Error('Respuesta no válida del servidor: ' + xhr.responseText));
            }
          } else {
            console.error("Error del servidor:", xhr.status, xhr.statusText, xhr.responseText);
            reject(new Error(`Error en la respuesta del servidor: ${xhr.status} ${xhr.statusText} - ${xhr.responseText}`));
          }
        };
        
        xhr.onerror = function(e) {
          console.error("Error de conexión:", e);
          reject(new Error('Error de conexión al servidor.'));
        };
        
        xhr.send(formData);
      }).then(data => {
        if (data.success) {
          showAlert('El servicio se ha actualizado correctamente.', true);
          setTimeout(() => {
            emit('updated', data.service);
            closeModal();
            location.reload();
          }, 1200);
        } else {
          showAlert(`Error al actualizar servicio: ${data.error || 'Error desconocido.'}`, false);
        }
      }).catch(e => {
        showAlert(`Error al actualizar servicio: ${e.message}`, false);
      });
    } else {
      // Si no hay imágenes nuevas, usar commonAPICall
      const data = await commonAPICall('http://localhost:8080/Apis/API_editar_servicio.php', 'POST', formData);
      if (data.success) {
        showAlert('El servicio se ha actualizado correctamente.', true);
        setTimeout(() => {
          emit('updated', data.service);
          closeModal();
          location.reload();
        }, 1200);
      } else {
        showAlert(`Error al actualizar servicio: ${data.error || 'Error desconocido.'}`, false);
      }
    }
  } catch (e) {
    showAlert(`Error al actualizar servicio: ${e.message}`, false);
  }
}


watch(
  () => estadoActivo.value,
  (val) => {
    estado.value = val ? 'Activo' : 'Oculto'
  }
)
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
.btn-primary:disabled {
  background-color: #a88c7a;
  cursor: not-allowed;
}
.btn-primary::after {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 5px;
  height: 5px;
  background: rgba(255, 255, 255, 0.5);
  opacity: 0;
  border-radius: 100%;
  transform: scale(1, 1) translate(-50%, -50%);
  transform-origin: 50% 50%;
}
.btn-primary:hover:not(:disabled)::after {
  animation: ripple 1s ease-out;
}
@keyframes ripple {
  0% {
    transform: scale(0, 0);
    opacity: 0.5;
  }
  100% {
    transform: scale(20, 20);
    opacity: 0;
  }
}
.btn-secondary {
  background-color: #B7CDDA;
  color: #431605;
  transition: all 0.3s ease;
}
.btn-secondary:hover {
  background-color: #BCAA81;
}
.form-label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: 500;
  color: #431605;
}
.form-input {
  width: 100%;
  padding: 0.5rem 0.75rem;
  border: 1px solid #BCAAA1;
  border-radius: 0.375rem;
  transition: border-color 0.2s;
}
.form-input:focus {
  outline: none;
  border-color: #825336;
  box-shadow: 0 0 0 3px rgba(130, 83, 54, 0.1);
}
.whitespace-pre-wrap {
  white-space: pre-wrap; /* Ensures error messages with newlines are displayed correctly */
}
</style>
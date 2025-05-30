<template>
  <div class="mb-12">
    <h1 class="text-3xl font-serif font-semibold mb-6 text-darktext">Configuración de Usuario</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Sección 1: Datos personales -->
      <div class="vintage-card p-6">
        <h2 class="text-xl font-serif mb-4 section-title">Datos Personales</h2>
        
        <div class="space-y-4" v-if="!loading">
          <div>
            <label class="block text-sm font-medium mb-1">Nombre</label>
            <input type="text" class="vintage-input w-full" v-model="usuario.nombre">
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">Apellidos</label>
            <input type="text" class="vintage-input w-full" v-model="usuario.apellidos">
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">Email</label>
            <input type="email" class="vintage-input w-full" v-model="usuario.email">
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">Teléfono</label>
            <input type="tel" class="vintage-input w-full" v-model="usuario.telefono">
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">Contraseña</label>
            <div class="flex items-center">
              <input type="password" class="vintage-input w-full" value="••••••••" readonly>
              <button class="vintage-button ml-3 px-4 py-2 text-sm flex items-center justify-center" @click="openChangePasswordModal">
                <i class="fas fa-key mr-2"></i> Cambiar
              </button>
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium mb-1">Login</label>
            <input type="text" class="vintage-input w-full bg-gray-100" v-model="usuario.login" readonly>
          </div>
          
          <div v-if="isAdmin">
            <label class="block text-sm font-medium mb-1">Rol</label>
            <select class="vintage-input w-full" v-model="usuario.rol">
              <option value="1">Administrador</option>
              <option value="2">Usuario</option>
            </select>
          </div>
          
        
          
          <button class="vintage-button mt-4 w-full" @click="guardarDatosPersonales">
            <i class="fas fa-save mr-2"></i> Guardar cambios
          </button>
        </div>
        <div v-else class="flex justify-center items-center h-64">
          <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-darkbutton"></div>
        </div>
      </div>
      
      <!-- Sección 2: Dirección -->
      <DireccionUsuario :userId="userId" :direccion="direccion" @actualizar="cargarDatos" />
      
      <!-- Sección 3: Métodos de pago -->
      <MetodosPago :userId="userId" />
    </div>
  </div>
  
  <!-- Modal para cambiar contraseña -->
  <CambiarPasswordModal v-if="showChangePasswordModal" @close="showChangePasswordModal = false" :userId="userId" />
</template>

<script setup>
import { ref, onMounted, computed, inject } from 'vue';
import DireccionUsuario from './DireccionUsuario.vue';
import MetodosPago from './MetodosPago.vue';
import CambiarPasswordModal from './CambiarPasswordModal.vue';

const usuario = ref({
  nombre: '',
  apellidos: '',
  email: '',
  telefono: '',
  login: '',
  rol: 2,
  fechaRegistro: null
});

const direccion = ref(null);
const loading = ref(true);
const userId = ref(null);
const showChangePasswordModal = ref(false);

// Inyectar la función showAlert desde el componente padre
const showAlert = inject('showAlert');

// Verificar si el usuario es administrador
const isAdmin = computed(() => {
  try {
    const userData = JSON.parse(localStorage.getItem('userData') || '{}');
    return userData && userData.role === 1;
  } catch {
    return false;
  }
});

// Cargar datos del usuario
const cargarDatos = async () => {
  loading.value = true;
  const startTime = Date.now();
  
  try {
    // Obtener el ID del usuario del localStorage
    const userData = JSON.parse(localStorage.getItem('userData') || '{}');
    userId.value = userData.userId;
    
    if (!userId.value) {
      console.error('No se encontró ID de usuario en localStorage');
      loading.value = false;
      return;
    }
    
    // Obtener datos del usuario
    const response = await fetch(`http://localhost:8080/Apis/API_obtener_usuario.php?id=${userId.value}`);
    const data = await response.json();
    
    if (data.success) {
      usuario.value = {
        nombre: data.usuario.USU_NOMBRE,
        apellidos: data.usuario.USU_APELLIDOS,
        email: data.usuario.USU_EMAIL,
        telefono: data.usuario.USU_TELEFONO || '',
        login: data.usuario.USU_LOGIN,
        rol: data.usuario.USU_ROL,
        fechaRegistro: data.usuario.USU_FECHAREGISTRO
      };
      
      // Obtener dirección del usuario
      if (data.direccion) {
        direccion.value = data.direccion;
      }
    } else {
      console.error('Error al cargar datos del usuario:', data.error);
      alert('Error al cargar tus datos. Por favor, intenta de nuevo más tarde.');
    }
  } catch (error) {
    console.error('Error en la API:', error);
    alert('Error al conectar con el servidor. Por favor, intenta de nuevo más tarde.');
  } finally {
    // Asegurar que el tiempo de carga no supere los 2 segundos ni sea demasiado rápido
    const elapsedTime = Date.now() - startTime;
    const minDisplayTime = 500; // Medio segundo mínimo para evitar parpadeos
    const maxDisplayTime = 2000; // 2 segundos máximo
    const displayTime = Math.min(Math.max(minDisplayTime - elapsedTime, 0), maxDisplayTime - elapsedTime);
    
    if (displayTime > 0) {
      setTimeout(() => {
        loading.value = false;
      }, displayTime);
    } else {
      loading.value = false;
    }
  }
};

// Guardar datos personales
const guardarDatosPersonales = async () => {
  try {
    const response = await fetch('http://localhost:8080/Apis/API_actualizar_usuario.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        id: userId.value,
        nombre: usuario.value.nombre,
        apellidos: usuario.value.apellidos,
        email: usuario.value.email,
        telefono: usuario.value.telefono,
        rol: usuario.value.rol
      })
    });
    
    const data = await response.json();
    
    if (data.success) {
      showAlert('Datos actualizados correctamente', true);
      // Actualizar datos en localStorage
      const userData = JSON.parse(localStorage.getItem('userData') || '{}');
      userData.name = usuario.value.nombre;
      userData.apellidos = usuario.value.apellidos;
      localStorage.setItem('userData', JSON.stringify(userData));
    } else {
      showAlert('Error al actualizar datos: ' + data.error, false);
    }
  } catch (error) {
    console.error('Error al actualizar datos:', error);
    showAlert('Error al conectar con el servidor', false);
  }
};

// Abrir modal para cambiar contraseña
const openChangePasswordModal = () => {
  showChangePasswordModal.value = true;
};

// Formatear fecha
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('es-ES', { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric' 
  });
};

onMounted(() => {
  cargarDatos();
});
</script>

<template>
  <div class="vintage-card p-6 mb-8">
    <h2 class="text-2xl font-serif mb-6 section-title">Administrador de Usuarios</h2>
    
    <!-- Buscador de usuarios por nombre o email -->
    <div class="mb-6 flex items-center">
      <div class="relative flex-grow">
        <span class="absolute left-3 top-1/2 transform -translate-y-1/4 text-darkbutton">
          <i class="fas fa-search"></i>
        </span>
        <input type="text" placeholder="Buscar usuarios..." 
               class="search-bar w-full pl-10"
               v-model="busqueda"
               @input="filtrarUsuarios">
      </div>
      <!-- Botón para mostrar/ocultar filtros -->
      <button class="vintage-button ml-4" @click="toggleFiltros">
        <i class="fas fa-filter mr-2"></i> Filtros
      </button>
    </div>
    
    <!-- Filtros avanzados (rol y orden) -->
    <div v-if="mostrarFiltros" class="bg-white p-4 mb-6 rounded-lg shadow-sm">
      <div class="flex flex-wrap gap-4">
        <div class="w-full sm:w-auto">
          <label class="block text-sm font-medium mb-1">Rol</label>
          <select class="vintage-input" v-model="filtroRol" @change="filtrarUsuarios">
            <option value="">Todos</option>
            <option value="1">Administrador</option>
            <option value="2">Usuario</option>
          </select>
        </div>
        <div class="w-full sm:w-auto">
          <label class="block text-sm font-medium mb-1">Ordenar por</label>
          <select class="vintage-input" v-model="orden" @change="filtrarUsuarios">
            <option value="nombre">Nombre</option>
            <option value="email">Email</option>
            <option value="fechaRegistro">Fecha de registro</option>
          </select>
        </div>
        <div class="w-full sm:w-auto mt-auto">
          <!-- Botón para limpiar todos los filtros -->
          <button class="vintage-button ml-3 px-4 py-2 text-sm flex items-center justify-center" @click="limpiarFiltros">
            <i class="fas fa-broom mr-2"></i> Limpiar filtros
          </button>
        </div>
      </div>
    </div>
    
    <!-- Tabla de usuarios con acciones -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-darkbutton"></div>
    </div>
    <div v-else>
      <div class="overflow-x-auto">
        <table class="min-w-full admin-table">
          <thead>
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Teléfono</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Rol</th>
              <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Acciones</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <!-- Itera sobre los usuarios filtrados y muestra cada uno -->
            <tr v-for="usuario in usuariosFiltrados" :key="usuario.id">
              <td class="px-6 py-4 whitespace-nowrap">{{ usuario.nombre }} {{ usuario.apellidos }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ usuario.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap">{{ usuario.telefono || '-' }}</td>
              <td class="px-6 py-4 whitespace-nowrap">
                <!-- Muestra el rol del usuario con color distintivo -->
                <span class="py-1 px-2 rounded-lg text-sm"
                      :class="usuario.rol == 1 ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800'">
                  {{ usuario.rol == 1 ? 'Administrador' : 'Usuario' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <!-- Botón para editar usuario -->
                <button class="text-darkbutton hover:text-button mr-3" @click="editarUsuario(usuario)">
                  <i class="fas fa-edit"></i>
                </button>
                <!-- Botón para eliminar usuario -->
                <button class="text-red-500 hover:text-red-700" @click="eliminarUsuario(usuario.id)">
                  <i class="fas fa-trash-alt"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Paginación de la tabla de usuarios -->
      <div class="flex items-center justify-between mt-6">
        <div class="text-sm text-gray-600">
          Mostrando {{ Math.min(1, usuariosFiltrados.length) }} a {{ Math.min(paginaActual * itemsPorPagina, usuariosFiltrados.length) }} de {{ usuariosFiltrados.length }} usuarios
        </div>
        <div class="flex space-x-2">
          <!-- Botón página anterior -->
          <button class="vintage-button-secondary px-3 py-1" 
                  @click="cambiarPagina(paginaActual - 1)" 
                  :disabled="paginaActual === 1">
            <i class="fas fa-chevron-left"></i>
          </button>
          <!-- Botones de número de página -->
          <button v-for="pagina in paginasTotales" 
                  :key="pagina" 
                  @click="cambiarPagina(pagina)" 
                  :class="[
                    'px-3 py-1',
                    pagina === paginaActual ? 'vintage-button' : 'vintage-button-secondary'
                  ]">
            {{ pagina }}
          </button>
          <!-- Botón página siguiente -->
          <button class="vintage-button-secondary px-3 py-1" 
                  @click="cambiarPagina(paginaActual + 1)" 
                  :disabled="paginaActual === paginasTotales">
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Modal de edición de usuario (teleportado al body) -->
  <Teleport to="body">
    <EditarUsuarioModal 
      v-if="usuarioEditar"
      :usuario="usuarioEditar"
      @close="cerrarModalEditarUsuario"
      @guardar="guardarCambiosUsuario"
    />
  </Teleport>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import EditarUsuarioModal from './EditarUsuarioModal.vue';

const usuarios = ref([]);
const usuariosFiltrados = ref([]);
const loading = ref(true);
const busqueda = ref('');
const filtroRol = ref('');
const orden = ref('nombre');
const mostrarFiltros = ref(false);
const usuarioEditar = ref(null); // Mantener solo esta declaración

// Emitir evento para editar usuario desde el componente padre
const emit = defineEmits(['editar-usuario']);

// Paginación
const paginaActual = ref(1);
const itemsPorPagina = 10;

// Calcular el total de páginas
const paginasTotales = computed(() => {
  return Math.ceil(usuariosFiltrados.value.length / itemsPorPagina);
});

// Cambiar de página
const cambiarPagina = (pagina) => {
  if (pagina >= 1 && pagina <= paginasTotales.value) {
    paginaActual.value = pagina;
  }
};

// Mostrar/ocultar filtros
const toggleFiltros = () => {
  mostrarFiltros.value = !mostrarFiltros.value;
};

// Limpiar filtros
const limpiarFiltros = () => {
  busqueda.value = '';
  filtroRol.value = '';
  orden.value = 'nombre';
  filtrarUsuarios();
};

// Filtrar usuarios
const filtrarUsuarios = () => {
  usuariosFiltrados.value = [...usuarios.value].filter(usuario => {
    const nombreCompleto = `${usuario.nombre} ${usuario.apellidos}`.toLowerCase();
    const busquedaLower = busqueda.value.toLowerCase();
    
    // Filtro por búsqueda
    const cumpleBusqueda = !busqueda.value || 
                          nombreCompleto.includes(busquedaLower) || 
                          usuario.email.toLowerCase().includes(busquedaLower);
    
    // Filtro por rol
    const cumpleRol = !filtroRol.value || usuario.rol.toString() === filtroRol.value;
    
    return cumpleBusqueda && cumpleRol;
  });
  
  // Ordenar
  usuariosFiltrados.value.sort((a, b) => {
    switch (orden.value) {
      case 'nombre':
        return `${a.nombre} ${a.apellidos}`.localeCompare(`${b.nombre} ${b.apellidos}`);
      case 'email':
        return a.email.localeCompare(b.email);
      case 'fechaRegistro':
        return new Date(b.fechaRegistro) - new Date(a.fechaRegistro); // Más reciente primero
      default:
        return 0;
    }
  });
  
  // Restablecer a la primera página después de filtrar
  paginaActual.value = 1;
};

// Cargar usuarios
const cargarUsuarios = async () => {
  loading.value = true;
  const startTime = Date.now();
  
  try {
    const response = await fetch('http://localhost:8080/Apis/API_listar_usuarios.php');
    const data = await response.json();
    
    if (data.success) {
      usuarios.value = data.usuarios.map(u => ({
        id: u.USU_USUARIO,
        nombre: u.USU_NOMBRE,
        apellidos: u.USU_APELLIDOS,
        email: u.USU_EMAIL,
        telefono: u.USU_TELEFONO,
        login: u.USU_LOGIN,
        rol: u.USU_ROL,
        fechaRegistro: u.USU_FECHAREGISTRO
      }));
      
      usuariosFiltrados.value = [...usuarios.value];
    } else {
      console.error('Error al cargar usuarios:', data.error);
      alert('Error al cargar la lista de usuarios');
    }
  } catch (error) {
    console.error('Error en la API:', error);
    alert('Error al conectar con el servidor');
  } finally {
    // Asegurar que el spinner se muestre por un tiempo razonable pero no más de 2 segundos
    const elapsed = Date.now() - startTime;
    const maxSpinnerTime = 2000; // 2 segundos máximo
    const minSpinnerTime = 500;  // 0.5 segundos mínimo
    
    const spinnerTimeLeft = Math.min(
      Math.max(minSpinnerTime - elapsed, 0),
      maxSpinnerTime - elapsed
    );
    
    if (spinnerTimeLeft > 0) {
      setTimeout(() => {
        loading.value = false;
      }, spinnerTimeLeft);
    } else {
      loading.value = false;
    }
  }
};

// Eliminar usuario
const eliminarUsuario = async (id) => {
  if (!confirm('¿Estás seguro de que deseas eliminar este usuario? Esta acción no se puede deshacer.')) {
    return;
  }
  
  try {
    const response = await fetch('http://localhost:8080/Apis/API_eliminar_usuario.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id })
    });
    
    const data = await response.json();
    
    if (data.success) {
      usuarios.value = usuarios.value.filter(usuario => usuario.id !== id);
      filtrarUsuarios(); // Actualizar filtros
      alert('Usuario eliminado correctamente');
    } else {
      alert('Error al eliminar usuario: ' + data.error);
    }
  } catch (error) {
    console.error('Error al eliminar usuario:', error);
    alert('Error al conectar con el servidor');
  }
};

// Editar usuario - Ya no emite evento, sino que abre el modal directamente
const editarUsuario = (usuario) => {
  usuarioEditar.value = { ...usuario };
};

// Cerrar modal de edición
const cerrarModalEditarUsuario = () => {
  usuarioEditar.value = null;
};

// Guardar cambios del usuario
const guardarCambiosUsuario = async (usuarioEditado) => {
  try {
    const response = await fetch('http://localhost:8080/Apis/API_actualizar_usuario.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(usuarioEditado)
    });
    
    const data = await response.json();
    
    if (data.success) {
      // Actualizar usuario en el array local
      const index = usuarios.value.findIndex(u => u.id === usuarioEditado.id);
      if (index !== -1) {
        usuarios.value[index] = { ...usuarioEditado };
        filtrarUsuarios(); // Actualizar filtros
      }
      
      cerrarModalEditarUsuario();
      alert('Usuario actualizado correctamente');
    } else {
      alert('Error al actualizar usuario: ' + data.error);
    }
  } catch (error) {
    console.error('Error al actualizar usuario:', error);
    alert('Error al conectar con el servidor');
  }
};

onMounted(() => {
  cargarUsuarios();
});
</script>

<style scoped>
.admin-table th {
  background-color: rgba(183, 205, 218, 0.5);
  font-weight: 600;
}

.admin-table tr:nth-child(even) {
  background-color: rgba(255, 255, 255, 0.6);
}

.admin-table tr:nth-child(odd) {
  background-color: rgba(255, 255, 255, 0.8);
}

.search-bar {
  background-color: rgba(255, 255, 255, 0.8);
  border: 1px solid #BCAEA1;
  border-radius: 20px;
  padding: 8px 15px 8px 36px; /* Incrementar el padding izquierdo para dar espacio al ícono */
  transition: all 0.3s;
  height: 42px; /* Altura específica para alinear bien con el icono */
}

.search-bar:focus {
  outline: none;
  border-color: #825336;
  box-shadow: 0 0 0 2px rgba(193, 143, 103, 0.3);
}
</style>

<script>
import mmodalsesion from "./mod_sesion/Modal_Sesion.vue";
import mmodalregistro from "./mod_sesion/Modal_registro.vue";

export default {
  components: {
    mmodalsesion,
    mmodalregistro,
  },
  data() {
    return {
      isSesionModalOpen: false, // Estado para el modal de sesión
      isRegistroModalOpen: false, // Estado para el modal de registro
      isMobileMenuOpen: false, // Estado para el menú móvil
      loggedIn: false,
      userName: '',
      userApellidos: '',
      isDropdownOpen: false, // Estado para el menú desplegable
    };
  },
  mounted() {
    this.checkSession();
  },
  computed: {
    userInitials() {
      const nameInitial = this.userName.charAt(0).toUpperCase();
      const apellidoInitial = this.userApellidos.charAt(0).toUpperCase();
      return `${nameInitial}${apellidoInitial}`;
    },
    
  },
  methods: {
    openSesionModal() {
      this.isSesionModalOpen = true;
      this.isRegistroModalOpen = false;
    },
    openRegistroModal() {
      this.isRegistroModalOpen = true;
      this.isSesionModalOpen = false;
    },
    closeSesionModal() {
      this.isSesionModalOpen = false;
    },
    closeRegistroModal() {
      this.isRegistroModalOpen = false;
    },
    switchToRegistro() {
      this.closeSesionModal();
      this.openRegistroModal();
    },
    switchToSesion() {
      this.closeRegistroModal();
      this.openSesionModal();
    },
    toggleMobileMenu() {
      this.isMobileMenuOpen = !this.isMobileMenuOpen;
    },
    checkSession() {
      const userData = localStorage.getItem("userData");
      if (userData) {
        const { name, apellidos } = JSON.parse(userData);
        this.userName = name;
        this.userApellidos = apellidos;
        this.loggedIn = true;
      } else {
        this.loggedIn = false;
      }
    },
    logout() {
      this.loggedIn = false;
      this.userName = '';
      this.userApellidos = '';
      this.isDropdownOpen = false; // Cerrar el menú desplegable
      localStorage.removeItem("userData");
      localStorage.removeItem("userId"); // Eliminar también el ID del usuario
    },
    toggleDropdown() {
      this.isDropdownOpen = !this.isDropdownOpen;
    },
    updateUser() {
      this.checkSession();
    },
  },
};
</script>

<template>
  <nav class="bg-[#B7CDDA] shadow-lg border-b-2 border-[#825336]">
    <div class="container mx-auto flex items-center justify-between p-4">
      <!-- Logo -->
      <a href="/" class="flex items-center space-x-3">
        <span class="text-2xl text-[#431605] font-poppins">011.PROJECT</span>
      </a>

      <!-- Menú móvil -->
      <button @click="toggleMobileMenu" class="md:hidden text-[#1F1E1E] focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
        </svg>
      </button>

      <!-- Menú de navegación -->
      <div
        :class="[ 
          'md:flex md:items-center md:justify-between w-full',
          isMobileMenuOpen ? 'block' : 'hidden',
          'absolute md:static top-16 left-0 bg-[#B7CDDA] md:bg-transparent p-4 md:p-0 transition-all duration-300'
        ]"
      >
        <!-- Listado centrado con margen -->
        <ul class="md:flex md:space-x-6 md:justify-center md:ml-16">
          <li class="text-center">
            <router-link to="/" exact-active-class="font-bold text-[#431605]"
              class="text-[#1F1E1E] hover:text-[#825336] transition-colors duration-300">
              Inicio
            </router-link>
          </li>
          <li class="text-center">
            <router-link to="/podcast" active-class="font-bold text-[#431605]"
              class="text-[#1F1E1E] hover:text-[#825336] transition-colors duration-300">
              Podcast
            </router-link>
          </li>
          <li class="text-center">
            <router-link to="/eventos" active-class="font-bold text-[#431605]"
              class="text-[#1F1E1E] hover:text-[#825336] transition-colors duration-300">
              Eventos
            </router-link>
          </li>
          
        </ul>

        <div v-if="loggedIn" class="md:flex md:space-x-4 md:justify-end items-center relative">
          <div @click="toggleDropdown" class="relative inline-flex items-center justify-center w-10 h-10 overflow-hidden bg-gray-100 rounded-full dark:bg-gray-600 cursor-pointer">
            <span class="font-medium text-gray-600 dark:text-gray-300">{{ userInitials }}</span>
          </div>
          <transition name="dropdown">
            <div v-if="isDropdownOpen" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg z-50">
              <ul class="py-2">
                <li>
                  <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Me gusta</a>
                </li>
                <li>
                  <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Favoritos</a>
                </li>
                <li>
                  <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Ajustes</a>
                </li>
                <li>
                  <button @click="logout" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Cerrar sesión</button>
                </li>
              </ul>
            </div>
          </transition>
        </div>
        <div v-else class="grid grid-cols-1 gap-4 mt-4 md:mt-0 md:flex md:space-x-4 md:justify-end">
          <button @click="openSesionModal"
            class="bg-[#DBE6ED] text-[#1F1E1E] hover:bg-[#C18F67] hover:text-white font-medium rounded-lg px-4 py-2 transition-all duration-300">
            Iniciar sesión
          </button>
          <button @click.prevent="openRegistroModal"
            class="bg-[#825336] text-white hover:bg-[#C18F67] font-medium rounded-lg px-4 py-2 transition-all duration-300">
            Regístrate
          </button>
        </div>
      </div>
    </div>
  </nav>

  <!-- Modal de sesión -->
  <mmodalsesion 
    :isOpen="isSesionModalOpen" 
    @close="closeSesionModal" 
    @switchToRegister="switchToRegistro" 
    @updateUser="updateUser" 
  />

  <!-- Modal de registro -->
  <mmodalregistro :isOpen="isRegistroModalOpen" @close="closeRegistroModal" @switchToLogin="switchToSesion" />
</template>

<style scoped>
/* Importar la fuente Poppins desde Google Fonts */
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

/* Estilo general del navbar */
nav {
  font-family: 'Arial', sans-serif;
}

/* Aplicar la fuente Poppins al texto del logo */
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

/* Transiciones suaves */
button,
a {
  transition: background-color 0.3s, color 0.3s, transform 0.2s;
}

button:hover {
  transform: scale(1.05);
}

ul li a {
  transition: color 0.3s;
}

/* Transición para el menú móvil */
ul {
  transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
}

/* Transición para el menú desplegable */
.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Asegurar que el div del usuario permanezca fijo */
.md\:flex.md\:space-x-4.md\:justify-end.items-center.relative {
  position: relative;
}
</style>

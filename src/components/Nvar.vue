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
      isSesionModalOpen: false,
      isRegistroModalOpen: false,
      isMobileMenuOpen: false,
      loggedIn: false,
      userName: '',
      userApellidos: '',
      isDropdownOpen: false,
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
      this.isMobileMenuOpen = false;
    },
    openRegistroModal() {
      this.isRegistroModalOpen = true;
      this.isSesionModalOpen = false;
      this.isMobileMenuOpen = false;
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
      this.isDropdownOpen = false;
      localStorage.removeItem("userData");
      localStorage.removeItem("userId");
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
  <!-- Navbar principal -->
  <nav class="navbar fixed top-0 left-0 right-0 z-50 py-4 px-6 md:px-12 bg-[#B7CDDA] border-b-2 border-[#825336] shadow-lg">
    <div class="container mx-auto flex flex-wrap items-center justify-between">
      <!-- Logo (ahora texto, con enlace a /) -->
      <div class="flex items-center">
        <a href="/" class="select-none">
          <span class="text-2xl font-bold font-poppins tracking-wide text-[#421706]">011.project</span>
        </a>
      </div>
      <!-- Botón menú móvil -->
      <button @click="toggleMobileMenu" class="md:hidden focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
      </button>
      <!-- Enlaces navegación desktop -->
      <div class="hidden md:flex md:items-center md:w-auto w-full" id="menu">
        <ul class="md:flex md:justify-center md:space-x-8">
          <li>
            <router-link to="/" exact-active-class="font-bold text-[#431605]" class="nav-link text-lg px-3 py-2" style="color: #1F1E1E;">
              Inicio
            </router-link>
          </li>
          <li>
            <router-link to="/podcast" active-class="font-bold text-[#431605]" class="nav-link text-lg px-3 py-2" style="color: #1F1E1E;">
              Podcast
            </router-link>
          </li>
          <li>
            <router-link to="/eventos" active-class="font-bold text-[#431605]" class="nav-link text-lg px-3 py-2" style="color: #1F1E1E;">
              Eventos
            </router-link>
          </li>
          <li>
            <router-link to="/servicios" active-class="font-bold text-[#431605]" class="nav-link text-lg px-3 py-2" style="color: #1F1E1E;">
              Servicios
            </router-link>
          </li>
        </ul>
      </div>
      <!-- Botones de autenticación desktop -->
      <div class="hidden md:flex space-x-4 items-center">
        <template v-if="loggedIn">
          <div class="relative">
            <div @click="toggleDropdown" class="inline-flex items-center justify-center w-10 h-10 overflow-hidden bg-gray-100 rounded-full cursor-pointer">
              <span class="font-medium text-gray-600">{{ userInitials }}</span>
            </div>
            <transition name="dropdown">
              <div v-if="isDropdownOpen" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg z-50">
                <ul class="py-2">
                  <li>
                    <a href="/ajustes" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Ajustes</a>
                  </li>
                  <li>
                    <button @click="logout" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Cerrar sesión</button>
                  </li>
                </ul>
              </div>
            </transition>
          </div>
        </template>
        <template v-else>
          <button @click="openSesionModal" class="btn-primary text-white px-6 py-2 rounded-full font-medium">Iniciar sesión</button>
          <button @click="openRegistroModal" class="btn-secondary text-white px-6 py-2 rounded-full font-medium">Regístrate</button>
        </template>
      </div>
    </div>
    <!-- Menú móvil -->
    <div class="md:hidden w-full mt-4" v-show="isMobileMenuOpen">
      <ul class="space-y-2">
        <li>
          <router-link to="/" exact-active-class="font-bold text-[#431605]" class="block px-3 py-2 rounded-md" style="background-color: #DBE6ED; color: #1F1E1E;" @click="isMobileMenuOpen = false">
            Inicio
          </router-link>
        </li>
        <li>
          <router-link to="/podcast" active-class="font-bold text-[#431605]" class="block px-3 py-2 rounded-md" style="background-color: #DBE6ED; color: #1F1E1E;" @click="isMobileMenuOpen = false">
            Podcast
          </router-link>
        </li>
        <li>
          <router-link to="/eventos" active-class="font-bold text-[#431605]" class="block px-3 py-2 rounded-md" style="background-color: #DBE6ED; color: #1F1E1E;" @click="isMobileMenuOpen = false">
            Eventos
          </router-link>
        </li>
        <li>
          <router-link to="/servicios" active-class="font-bold text-[#431605]" class="block px-3 py-2 rounded-md" style="background-color: #DBE6ED; color: #1F1E1E;" @click="isMobileMenuOpen = false">
            Servicios
          </router-link>
        </li>
        <template v-if="loggedIn">
          <li class="pt-2">
            <button @click="logout" class="btn-primary text-white w-full px-4 py-2 rounded-full font-medium">Cerrar sesión</button>
          </li>
        </template>
        <template v-else>
          <li class="pt-2">
            <button @click="openSesionModal" class="btn-primary text-white w-full px-4 py-2 rounded-full font-medium">Iniciar sesión</button>
          </li>
          <li>
            <button @click="openRegistroModal" class="btn-secondary text-white w-full px-4 py-2 rounded-full font-medium">Regístrate</button>
          </li>
        </template>
      </ul>
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
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

.navbar {
  background-color: #B7CDDA;
  border-bottom: 2px solid #825336;
  font-family: 'Arial', sans-serif;
}

.font-poppins {
  font-family: 'Poppins', sans-serif;
}

.btn-primary {
  background-color: #825336;
  transition: background-color 0.3s, color 0.3s, transform 0.2s;
}
.btn-primary:hover {
  background-color: #C18F67;
  color: #fff;
  transform: scale(1.05);
}

.btn-secondary {
  background-color: #C18F67;
  transition: background-color 0.3s, color 0.3s, transform 0.2s;
}
.btn-secondary:hover {
  background-color: #825336;
  color: #fff;
  transform: scale(1.05);
}

.nav-link {
  transition: color 0.3s;
  border-radius: 0.375rem;
}
.nav-link:hover {
  color: #825336 !important;
  background-color: #DBE6ED;
}

.dropdown-enter-active,
.dropdown-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}
.dropdown-enter-from,
.dropdown-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>

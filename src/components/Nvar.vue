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
    };
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
            <router-link to="/eventos" active-class="font-bold text-[#431605]"
              class="text-[#1F1E1E] hover:text-[#825336] transition-colors duration-300">
              Eventos
            </router-link>
          </li>
        </ul>

        <div v-if="loggedIn" class="md:flex md:space-x-4 md:justify-end">
          <span class="text-[#1F1E1E] font-medium">Bienvenido {{ userName }} {{ userApellidos }}</span>
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
  <mmodalsesion :isOpen="isSesionModalOpen" @close="closeSesionModal" @switchToRegister="switchToRegistro" />

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
</style>

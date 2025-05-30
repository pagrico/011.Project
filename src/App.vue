<script setup>
import { RouterView } from 'vue-router';
import Nvar from './components/Nvar.vue';
import Footer from './components/Footer.vue';
import { ref, provide } from 'vue';
import ModalAlert from './components/Common/ModalAlert.vue';


// Estado para el modal de alerta
const showAlert = ref(false);
const alertMessage = ref('');
const alertSuccess = ref(true);

// Función para mostrar el modal
const showAlertModal = (message, isSuccess = true) => {
  alertMessage.value = message;
  alertSuccess.value = isSuccess;
  showAlert.value = true;
};

// Proporcionar la función para que los componentes hijos puedan usarla
provide('showAlert', showAlertModal);
</script>

<template>
  <!-- Barra de navegación centrada -->
  <div class="flex justify-center w-full">
    <Nvar />
  </div>

  <!-- Contenido principal -->
  <main class="app-container">
    <RouterView />
  </main>
  <!-- Footer -->
  <Footer />

  <!-- Componente global para modales de alerta -->
  <ModalAlert 
    v-if="showAlert"
    :message="alertMessage"
    :isSuccess="alertSuccess"
    @close="showAlert = false"
  />
</template>

<style scoped>
/* Estilo general para la aplicación */
.app-container {
  padding: 20px; /* Espaciado interno para el contenido */
  background-color: #DBE6ED; /* Fondo azul claro relacionado con el proyecto */
  min-height: 100vh; /* Asegura que el contenido ocupe toda la altura de la pantalla */
  font-family: 'Arial', sans-serif; /* Fuente predeterminada */
  color: #1F1E1E; /* Color del texto */
  box-sizing: border-box; /* Asegura que el padding no afecte el tamaño total */
}

/* Estilo para enlaces dentro de la aplicación */
a {
  color: #825336; /* Color marrón para los enlaces */
  text-decoration: none; /* Sin subrayado */
  transition: color 0.3s ease; /* Transición suave al pasar el mouse */
}

a:hover {
  color: #C18F67; /* Color más claro al pasar el mouse */
}
</style>

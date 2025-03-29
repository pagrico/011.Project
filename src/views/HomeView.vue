<script setup>
import { ref, onMounted } from 'vue';

// Estado reactivo para mostrar/ocultar el botón
const showScrollTopButton = ref(false);

// Función para manejar el efecto de aparición al hacer scroll
const handleScroll = () => {
  const sections = document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right');
  sections.forEach((section) => {
    const rect = section.getBoundingClientRect();
    if (rect.top < window.innerHeight) {
      section.classList.add('visible');
    }
  });

  // Mostrar el botón al pasar la sección "Quienes Somos"
  const quienesSomosSection = document.querySelector('#section-quienes-somos');
  if (quienesSomosSection) {
    const rect = quienesSomosSection.getBoundingClientRect();
    showScrollTopButton.value = rect.top < window.innerHeight;
  }
};

// Función para desplazarse al inicio
const scrollToTop = () => {
  window.scrollTo({
    top: 0,
    behavior: 'smooth',
  });
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll);
});
</script>

<template>
  <main>
    <!-- Botón para subir al inicio -->
    <button
      v-if="showScrollTopButton"
      @click="scrollToTop"
      class="fixed bottom-8 right-8 bg-[#825336] text-white font-bold py-2 px-4 rounded-full shadow-lg hover:bg-[#C18F67] transition-all duration-300 z-50"
    >
      ↑
    </button>

    <!-- Sección principal -->
    <section id="section-main" class="min-h-screen flex flex-col justify-center items-center bg-[#DBE6ED] rounded-lg shadow-md">
      <h1>Que es 011.project</h1>
      <p>Una plataforma para la gestión de eventos y actividades.</p>
    </section>

    <!-- Sección Quienes Somos -->
    <section id="section-quienes-somos" class="min-h-screen flex flex-col justify-center items-center bg-[#A6BCCB] rounded-lg shadow-md">
      <h1 class="text-5xl font-bold text-[#431605] mt-12 mb-12 font-poppins text-center">Quienes Somos</h1>

      <!-- Pablo -->
      <div class="fade-in flex flex-col md:flex-row items-center md:items-start w-full px-4 mb-12 bg-[#B7CDDA] py-8 rounded-lg shadow-md">
        <img
          src="../assets/Pablo.png"
          alt="Imagen de Pablo"
          class="fade-in-left w-full md:w-1/2 max-w-sm rounded-lg shadow-md drop-shadow-lg"
        />
        <div class="md:ml-6 mt-4 md:mt-0 text-left">
          <h2 class="text-2xl font-bold text-[#431605] text-center md:text-left">Pablo</h2>
          <ul class="list-disc list-inside text-[#1F1E1E]">
            <li>Aquí va un apartado</li>
            <li>Aquí va otro apartado</li>
            <li>Otro apartado más</li>
          </ul>
        </div>
      </div>

      <!-- Noelia -->
      <div class="fade-in flex flex-col md:flex-row-reverse items-center md:items-start w-full px-4 bg-[#DBE6ED] py-8 rounded-lg shadow-md">
        <img
          src="../assets/Noe.png"
          alt="Imagen de Noelia"
          class="fade-in-right w-full md:w-1/2 max-w-sm rounded-lg shadow-md drop-shadow-lg"
        />
        <div class="md:mr-6 mt-4 md:mt-0 text-left">
          <h2 class="text-2xl font-bold text-[#431605] text-center md:text-left">Noelia</h2>
          <ul class="list-disc list-inside text-[#1F1E1E]">
            <li>Aquí va un apartado</li>
            <li>Aquí va otro apartado</li>
            <li>Otro apartado más</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="min-h-screen flex justify-center items-center bg-[#DBE6ED] rounded-lg shadow-md">
      <p>© 2025 Mi Proyecto</p>
    </footer>
  </main>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

main {
  font-family: Arial, sans-serif;
  text-align: center;
  color: #431605; /* Texto principal */
}

.font-poppins {
  font-family: 'Poppins', sans-serif;
}

section {
  margin: 20px 0;
}

p {
  color: #431605; /* Texto de párrafos */
}

footer {
  font-size: 0.9em;
  color: #825336; /* Marrón para el pie de página */
  border-top: 2px solid #825336; /* Línea marrón */
  padding-top: 10px;
}

/* Efecto de aparición */
.fade-in {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 1s ease-out, transform 1s ease-out;
}

.fade-in-left {
  opacity: 0;
  transform: translateX(-50px);
  transition: opacity 1.5s ease-out, transform 1.5s ease-out;
}

.fade-in-right {
  opacity: 0;
  transform: translateX(50px);
  transition: opacity 1.5s ease-out, transform 1.5s ease-out;
}

.fade-in.visible,
.fade-in-left.visible,
.fade-in-right.visible {
  opacity: 1;
  transform: translateX(0);
  transform: translateY(0);
}

/* Altura mínima para ocupar toda la pantalla */
.min-h-screen {
  min-height: 100vh;
}

/* Ajuste de tamaño máximo para las imágenes */
img {
  max-width: 100%; /* Asegura que las imágenes no excedan el ancho del contenedor */
  height: auto; /* Mantiene la proporción de las imágenes */
}

.max-w-sm {
  max-width: 300px; /* Tamaño máximo para las imágenes */
}

/* Nueva clase para sombras en imágenes */
.drop-shadow-lg {
  filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.3));
}
</style>

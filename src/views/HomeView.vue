<script setup>
import { ref, onMounted } from 'vue';
import QuienesSomos from '../components/Quienes_somos.vue'; // Importación corregida

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
      <h1 class="text-6xl sm:text-4xl font-bold text-[#431605] font-poppins mb-4">Que es 011.project</h1>
      <p class="text-lg text-[#1F1E1E]">Una plataforma para la gestión de eventos y actividades.</p>
    </section>

    <!-- Sección Quienes Somos -->
    <section id="section-quienes-somos" class="min-h-screen flex flex-col justify-center items-center bg-[#A6BCCB] rounded-lg shadow-md p-8">
      <h1 class="text-5xl sm:text-3xl font-bold text-[#431605] mt-12 mb-12 font-poppins text-center">Quienes Somos</h1>
      <QuienesSomos />
    </section>

    <!-- Footer -->
    <footer id="footer" class="min-h-screen flex justify-center items-center bg-[#DBE6ED] rounded-lg shadow-md">
      <p class="text-[#825336]">© 2025 Mi Proyecto</p>
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
  max-width: 100%;
  height: auto; 
}

.max-w-sm {
  max-width: 300px;
}

/* Nueva clase para sombras en imágenes */
.drop-shadow-lg {
  filter: drop-shadow(0 15px 25px rgba(0, 0, 0, 0.3));
}

/* Estilo base para tarjetas */
.card {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  position: relative;
  overflow: hidden;
  width: 300px; /* Ajusta el ancho según tus necesidades */
  margin: 0 auto; /* Para centrar la tarjeta dentro de la columna */
  min-height: 350px; /* Altura mínima para mostrar contenido y permitir el slide horizontal */
}

.card:hover {
  transform: translateY(-10px);
}

.card img {
  transition: transform 0.5s ease;
}

/* 
   Contenido desplegable compartido:
   - Ocultamos el bloque (text-block) desplazándolo horizontalmente y con opacity 0.
   - Al hacer hover en la tarjeta, se deslizará y se hará visible.
   - Ajustamos display: flex para centrar el contenido en medio.
*/
.text-block {
  position: absolute;
  top: 0;
  bottom: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  padding: 1rem;
  background-color: rgba(255, 255, 255, 0.7); /* Ejemplo de fondo semitransparente */
  box-sizing: border-box;
  opacity: 0;
  transition: transform 0.5s ease, opacity 0.5s ease;
}

/* Tarjeta de Pablo: se despliega desde la derecha */
.card-pablo .text-block {
  right: 0;
  /* Iniciamos con un translate positivo para que venga desde la derecha (100% = ancho total) */
  transform: translateX(100%);
}

.card-pablo:hover .text-block {
  transform: translateX(0);
  opacity: 1;
}

/* Tarjeta de Noelia: se despliega desde la izquierda */
.card-noelia .text-block {
  left: 0;
  /* Iniciamos con un translate negativo para que venga desde la izquierda */
  transform: translateX(-100%);
}

.card-noelia:hover .text-block {
  transform: translateX(0);
  opacity: 1;
}
</style>

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
  <!-- Main Content -->
  <main class="mt-20 pt-8 pb-16 px-6 md:px-12">
    <!-- Hero Section -->
    <section id="inicio" class="mb-20">
      <div class="container mx-auto">
        <div class="section p-8 md:p-12">
          <h1 class="text-4xl md:text-5xl font-bold mb-8 text-center font-poppins" style="color: #431605;">¿Qué es 011.Project?</h1>
          <!-- Image Gallery -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="gallery-item overflow-hidden rounded-lg vintage-border flex items-center justify-center" style="height: 288px;">
              <img src="../assets/Estudio1.png" 
                   alt="Setup podcast" class="w-full h-full object-cover object-center transition duration-500 hover:scale-105" style="aspect-ratio: 4/3;">
            </div>
            <div class="gallery-item overflow-hidden rounded-lg vintage-border flex items-center justify-center" style="height: 288px;">
              <img src="../assets/Estudio2.jpg" 
                   alt="Comienzos" class="w-full h-full object-cover object-center transition duration-500 hover:scale-105" style="aspect-ratio: 4/3;">
            </div>
            <div class="gallery-item overflow-hidden rounded-lg vintage-border flex items-center justify-center" style="height: 288px;">
              <img src="..\assets\Estudio3.png" 
                   alt="Podcast" class="w-full h-full object-cover object-center transition duration-500 hover:scale-105" style="aspect-ratio: 4/3;">
            </div>
          </div>
          <div class="max-w-3xl mx-auto">
            <p class="text-lg mb-6" style="color: #1F1E1E;">
              011.Project comenzó como un podcast centrado en el emprendimiento empresarial, pero ha evolucionado para convertirse en mucho más. 
              Lo que empezó como conversaciones sobre negocios y estrategias, se ha transformado en una plataforma integral que ofrece servicios 
              profesionales especializados para empresas y particulares.
            </p>
            <p class="text-lg mb-6" style="color: #1F1E1E;">
              Contamos con un equipo de agentes expertos en imagen corporativa, publicidad y branding empresarial, listos para ayudarte a llevar 
              tu proyecto al siguiente nivel. Nuestra misión es combinar el conocimiento práctico del emprendimiento con soluciones profesionales 
              de alta calidad.
            </p>
            <p class="text-lg" style="color: #1F1E1E;">
              Ya sea que estés comenzando tu negocio o buscando escalar tu empresa establecida, 011.Project ofrece los recursos, la orientación 
              y los servicios que necesitas para tener éxito en el competitivo mundo empresarial actual.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <section id="quienes-somos" class="mb-20 fade-in">
      <div class="container mx-auto">
        <div class="section p-8 md:p-12">
          <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center font-poppins" style="color: #431605;">¿Quiénes somos?</h2>
          <!-- Contenido HTML del prompt -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="team-card p-6 rounded-lg">
              <h3 class="text-2xl font-bold mb-4 font-poppins" style="color: #825336;">Nuestra Historia</h3>
              <p class="mb-4" style="color: #1F1E1E;">
                011.Project nació de la pasión por el emprendimiento y la necesidad de crear un espacio donde los empresarios pudieran encontrar 
                no solo inspiración, sino también herramientas prácticas para sus negocios.
              </p>
              <p class="mb-4" style="color: #1F1E1E;">
                Comenzamos como un modesto podcast en 2018, compartiendo historias de emprendedores y analizando casos de estudio. Con el tiempo, 
                nuestra comunidad creció y con ella, nuestra capacidad para ofrecer servicios más especializados.
              </p>
              <p style="color: #1F1E1E;">
                Hoy somos un equipo multidisciplinario de profesionales comprometidos con el crecimiento empresarial de nuestros clientes y oyentes.
              </p>
            </div>
            <div class="team-card p-6 rounded-lg">
              <h3 class="text-2xl font-bold mb-4 font-poppins" style="color: #825336;">Nuestro Equipo</h3>
              <p class="mb-4" style="color: #1F1E1E;">
                Contamos con especialistas en diversas áreas del marketing y desarrollo empresarial:
              </p>
              <ul class="space-y-2" style="color: #1F1E1E;">
                <li class="flex items-center">
                  <span class="inline-block w-2 h-2 rounded-full mr-2" style="background-color: #BCA1A1;"></span>
                  Expertos en branding e identidad corporativa
                </li>
                <li class="flex items-center">
                  <span class="inline-block w-2 h-2 rounded-full mr-2" style="background-color: #BCA1A1;"></span>
                  Estrategas de marketing digital
                </li>
                <li class="flex items-center">
                  <span class="inline-block w-2 h-2 rounded-full mr-2" style="background-color: #BCA1A1;"></span>
                  Consultores en desarrollo empresarial
                </li>
                <li class="flex items-center">
                  <span class="inline-block w-2 h-2 rounded-full mr-2" style="background-color: #BCA1A1;"></span>
                  Diseñadores gráficos y de experiencia de usuario
                </li>
                <li class="flex items-center">
                  <span class="inline-block w-2 h-2 rounded-full mr-2" style="background-color: #BCA1A1;"></span>
                  Productores de contenido multimedia
                </li>
              </ul>
            </div>
          </div>
          <!-- Debajo, el componente QuienesSomos -->
          <div class="mt-16">
            <QuienesSomos />
          </div>
        </div>
      </div>
    </section>

    <!-- Social Media Section -->
    <section id="redes" class="mb-20 fade-in">
      <div class="container mx-auto">
        <div class="section p-8 md:p-12">
          <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center font-poppins" style="color: #431605;">Síguenos en redes</h2>
          <div class="flex flex-wrap justify-center gap-8 md:gap-16">
          <a href="https://www.instagram.com/011.project/?hl=en" class="flex flex-col items-center group" target="_blank" rel="noopener">
            <div class="social-icon mb-2" style="color: #BCA1A1;">
            <i class="fab fa-instagram"></i>
            </div>
            <span class="text-lg font-medium group-hover:underline" style="color: #1F1E1E;">Instagram</span>
          </a>
          <a href="https://www.youtube.com/channel/UC9D4KMFefAZj1ra3h-BX2fg" class="flex flex-col items-center group" target="_blank" rel="noopener">
            <div class="social-icon mb-2" style="color: #BCA1A1;">
            <i class="fab fa-youtube"></i>
            </div>
            <span class="text-lg font-medium group-hover:underline" style="color: #1F1E1E;">YouTube</span>
          </a>
          <a href="https://open.spotify.com/show/0R69JWnRzzmYl3weiLtTab" class="flex flex-col items-center group" target="_blank" rel="noopener">
            <div class="social-icon mb-2" style="color: #BCA1A1;">
            <i class="fab fa-spotify"></i>
            </div>
            <span class="text-lg font-medium group-hover:underline" style="color: #1F1E1E;">Spotify</span>
          </a>
          <a href="https://www.linkedin.com/company/011-project/" class="flex flex-col items-center group" target="_blank" rel="noopener">
            <div class="social-icon mb-2" style="color: #BCA1A1;">
            <i class="fab fa-linkedin"></i>
            </div>
            <span class="text-lg font-medium group-hover:underline" style="color: #1F1E1E;">LinkedIn</span>
          </a>
          <a href="https://www.tiktok.com/@011.project" class="flex flex-col items-center group" target="_blank" rel="noopener">
            <div class="social-icon mb-2" style="color: #BCA1A1;">
            <i class="fab fa-tiktok"></i>
            </div>
            <span class="text-lg font-medium group-hover:underline" style="color: #1F1E1E;">TikTok</span>
          </a>
          <a href="https://www.facebook.com/profile.php?id=100095475000810" class="flex flex-col items-center group" target="_blank" rel="noopener">
            <div class="social-icon mb-2" style="color: #BCA1A1;">
            <i class="fab fa-facebook"></i>
            </div>
            <span class="text-lg font-medium group-hover:underline" style="color: #1F1E1E;">Facebook</span>
          </a>
          </div>
        </div>
      </div>
    </section>
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

.social-icon {
  font-size: 2rem;
  color: #BCA1A1;
  margin-bottom: 1rem;
  transition: transform 0.3s, color 0.3s;
}
.group:hover .social-icon,
.group:focus .social-icon {
  transform: scale(1.2);
  color: #825336 !important; /* Marrón oscuro */
}
</style>

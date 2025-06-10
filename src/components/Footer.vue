<template>
  <!-- Pie de página con información, enlaces y contacto -->
  <footer class="py-12 px-6 md:px-12 ">
    <div class="container mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div>
          <h3 class="text-xl font-bold mb-4" style="color: #DBE6ED;">011.Project</h3>
          <p class="mb-4" style="color: #B7CDDA;">
            Tu aliado en emprendimiento empresarial. Podcast, servicios profesionales y comunidad.
          </p>
          <p style="color: #B7CDDA;">
            © 2023 011.Project. Todos los derechos reservados.
          </p>
        </div>
        <div>
          <h3 class="text-xl font-bold mb-4" style="color: #DBE6ED;">Enlaces rápidos</h3>
          <ul class="space-y-2">
            <li><a href="#inicio" class="hover:underline" style="color: #B7CDDA;">Inicio</a></li>
            <li><a href="/podcast" class="hover:underline" style="color: #B7CDDA;">Podcast</a></li>
            <li><a href="/eventos" class="hover:underline" style="color: #B7CDDA;">Eventos</a></li>
            <li><a href="/servicios" class="hover:underline" style="color: #B7CDDA;">Servicios</a></li>
          </ul>
        </div>
        <div>
          <h3 class="text-xl font-bold mb-4" style="color: #DBE6ED;">Contacto</h3>
          <ul class="space-y-2">
            <li class="flex items-center">
              <i class="fas fa-envelope mr-2" style="color: #BCA1A1;"></i>
              <span style="color: #B7CDDA;">ceroonceproject@gmail.com</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-phone mr-2" style="color: #BCA1A1;"></i>
              <span style="color: #B7CDDA;">+1 234 567 890</span>
            </li>
            <li class="flex items-center">
              <i class="fas fa-map-marker-alt mr-2" style="color: #BCA1A1;"></i>
              <span style="color: #B7CDDA;">España, Valencia</span>
            </li>
          </ul>
        </div>
      </div>
      <div class="border-t border-gray-700 mt-8 pt-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="mb-4 md:mb-0">
            <a href="#" class="text-sm hover:underline mr-4" style="color: #B7CDDA;">Términos y condiciones</a>
            <a href="#" class="text-sm hover:underline" style="color: #B7CDDA;">Política de privacidad</a>
          </div>
          <div>
            <p class="text-sm" style="color: #B7CDDA;">
              Diseñado con ❤️ por el equipo de 011.Project
            </p>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
// No se requiere lógica JS para este footer
</script>

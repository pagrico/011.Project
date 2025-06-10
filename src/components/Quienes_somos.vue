<template>
  <!-- Sección de presentación de los miembros del equipo -->
  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 px-4">
    <!-- Tarjeta Pablo -->
    <div class="card">
      <div class="img-bx"><img src="../assets/pablo.png" alt=""></div>
      <div class="content">
        <h3>Pablo <br><span>Product Manager</span></h3>
        <ul class="list-none space-y-3">
          <li class="flex items-start" style="color: #431605;">
            <span class="inline-block w-3 h-3 rounded-full mr-3 mt-1 flex-shrink-0" style="background-color: #825336;"></span>
            Fotógrafo y videógrafo profesional con más de 8 años de experiencia en producción audiovisual.
          </li>
          <li class="flex items-start" style="color: #431605;">
            <span class="inline-block w-3 h-3 rounded-full mr-3 mt-1 flex-shrink-0" style="background-color: #BCA1A1;"></span>
            Especializado en retrato, eventos y edición de video.
          </li>
          <li class="flex items-start" style="color: #431605;">
            <span class="inline-block w-3 h-3 rounded-full mr-3 mt-1 flex-shrink-0" style="background-color: #825336;"></span>
            Ha colaborado con marcas y artistas para crear contenido visual impactante.
          </li>
          <li class="flex items-start" style="color: #431605;">
            <span class="inline-block w-3 h-3 rounded-full mr-3 mt-1 flex-shrink-0" style="background-color: #BCA1A1;"></span>
            Apasionado por la creatividad y la innovación tecnológica en el sector audiovisual.
          </li>
        </ul>
      </div>
    </div>

    <!-- Tarjeta Noelia -->
    <div class="card reverse">
      <div class="content">
        <h3>Noelia <br><span>Diseñadora</span></h3>
        <ul class="list-none space-y-3">
          <li class="flex items-start" style="color: #431605;">
            <span class="inline-block w-3 h-3 rounded-full mr-3 mt-1 flex-shrink-0" style="background-color: #825336;"></span>
            Experta en gestión de redes sociales y emprendimiento digital.
          </li>
          <li class="flex items-start" style="color: #431605;">
            <span class="inline-block w-3 h-3 rounded-full mr-3 mt-1 flex-shrink-0" style="background-color: #BCA1A1;"></span>
            Ha liderado proyectos de branding y estrategias de marketing online para pequeñas empresas y startups.
          </li>
          <li class="flex items-start" style="color: #431605;">
            <span class="inline-block w-3 h-3 rounded-full mr-3 mt-1 flex-shrink-0" style="background-color: #825336;"></span>
            Creativa, analítica y con habilidades de comunicación para potenciar la presencia digital de marcas emergentes.
          </li>
          <li class="flex items-start" style="color: #431605;">
            <span class="inline-block w-3 h-3 rounded-full mr-3 mt-1 flex-shrink-0" style="background-color: #BCA1A1;"></span>
            Emprendedora con visión estratégica y pasión por el desarrollo de negocios digitales.
          </li>
        </ul>
      </div>
      <div class="img-bx"><img src="../assets/Noe.png" alt=""></div>
    </div>
  </div>
</template>

<style scoped>
body {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Poppins', sans-serif;
  justify-content: center;
  align-items: center;
  display: flex;
  min-height: 100vh;
  background: #333;
}

.grid {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 50px;
}

.card {
    padding-top: 5%;
  position: relative;
  width: 400px;
  height: 600px;
  background: #fff;
  border-radius: 40px;
  box-shadow: 0 20px 25px rgba(0, 0, 0, 0.25);
  transition: 1.2s;
  overflow: hidden;
  transition: width 1.2s ease-in-out; /* Ajustado a 1.2 segundos */
}

.card:hover {
  width: 800px; /* Expande el card horizontalmente */
}

.img-bx {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden; /* Asegura que la imagen no se salga del contenedor */
  
}

.img-bx img {
  width: 100%; /* Ajusta el ancho al 100% del contenedor */
  height: 100%; /* Ajusta la altura al 100% del contenedor */
  object-fit: contain; /* Evita recortes en la imagen */
  object-position: center bottom; /* Pega la imagen al fondo del card */
  transform: scale(1) translateX(0); /* Posición inicial */
  transition: transform 1.2s ease-in-out, object-position 1.2s ease-in-out; /* Ajustado a 1.2 segundos */
  filter: drop-shadow( 10px 10px 10px rgba(0, 0, 0, 0.5)); /* Aplica sombra al contorno de la imagen */
}

.card:nth-child(1) .img-bx img {
  filter: drop-shadow(-10px 10px 10px rgba(0, 0, 0, 0.5)); /* Sombra hacia la izquierda */
  margin-top: 10px; /* Asegura que no se corte con la parte superior */
}

.card:nth-child(2) .img-bx img {
  filter: drop-shadow(10px 10px 10px rgba(0, 0, 0, 0.5)); /* Sombra hacia la derecha */
  margin-top: 10px; /* Asegura que no se corte con la parte superior */
}

.card:hover .img-bx img {
  transform: scale(1.02); /* Reduce el zoom para evitar que la imagen se salga */
}

.card .img-bx img {
  transform: scale(1) translateX(0); /* Transición para volver al estado original */
  object-position: center bottom; /* Posición inicial */
}

.card:nth-child(1):hover .img-bx img {
  object-position: left bottom; /* Pega la imagen a la izquierda abajo */
  transform: scale(1) translateX(20px); /* Margen de 20px desde la izquierda */
}

.card:nth-child(2):hover .img-bx img {
  object-position: right bottom; /* Pega la imagen a la derecha abajo */
  transform: scale(1) translateX(-20px); /* Margen de 20px desde la derecha */
}

.content {
  position: absolute;
  top: 0;
  left: 95%; /* Reduce el espacio entre el contenido y la imagen */
  width: 400px;
  height: 200%;
  z-index: 1; /* Por debajo de la imagen cuando no hay hover */
  padding: 20px;
  background: none;
  opacity: 0; /* Oculta el contenido inicialmente */
  transition: opacity 1.2s ease-in-out, transform 1.2s ease-in-out; /* Ajustado a 1.2 segundos */
}

.card.reverse .content {
  left: auto;
  right: 95%; /* Reduce el espacio entre el contenido y la imagen */
}

.card.reverse:hover .content {
  transform: translateX(100%); /* Mueve el contenido hacia la derecha para que se alinee con el card expandido */
}

.card:hover .content {
  opacity: 1; /* Muestra el contenido */
  transform: translateX(-100%); /* Mueve el contenido hacia la izquierda para que se alinee con el card expandido */
  z-index: 10; /* Asegura que el contenido quede por encima con hover */
}

.card .content {
  transition: opacity 1.2s ease-in-out, transform 1.2s ease-in-out; /* Ajustado a 1.2 segundos */
}

.content h3 {
  font-size: 1.5rem; /* Aumenta el tamaño del título */
  color: #333;
}

.content span {
  font-weight: 400;
  font-size: 0.75rem; /* Ajusta el tamaño del subtítulo */
  opacity: 0.75;
}

.content p {
  margin-top: 10px;
  font-size: 1rem; /* Aumenta el tamaño del texto */
  color: #333;
}

@media (max-width: 768px) {
  .card {
    width: 80vw; /* Reduce el ancho para pantallas pequeñas */
    height: 400px; /* Ajusta la altura para que no resulte muy grande */
  }

  .card:hover {
    width: 80vw; /* Evita que la tarjeta se expanda demasiado en móviles */
  }

  .content {
    width: 80vw; /* Ajusta también el ancho del contenido */
    height: auto; /* Permite que la altura se adapte */
  }
}

@media (max-width: 1024px) {
  .card {
    width: 350px; /* Ajusta la tarjeta en tablets */
    height: 500px; /* Ajusta la altura */
  }
  .card:hover {
    width: 350px; /* Evita expansión en tablets */
  }
  .content {
    width: 350px;
    background: white; /* Ajusta contenido en tablets */
  }
}

@media (max-width: 480px) {
  .grid {
    display: grid;
    grid-template-columns: 1fr; /* Forzar una sola columna en móvil */
  }

  .card {
    width: 90vw; /* Más adecuado para pantallas pequeñas */
    margin: 0 auto;
    height: auto;
  }
  .card:hover {
    width: 90vw; /* Evita expansión en móviles */
  }
  .content {
    position: static; /* Quita el posicionamiento absoluto */
    width: 100%;
    height: auto;
    transform: none;
    opacity: 1; /* Muestra el contenido siempre en móviles */
    background: white; /* Fondo blanco en responsive */
  }
  .card.reverse .content,
  .card.reverse:hover .content,
  .card:hover .content {
    transform: none;
    opacity: 1;
  }
}
</style>

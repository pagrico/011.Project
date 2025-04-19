<template>
    <div>
        <div v-if="cargando" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div v-for="n in 12" :key="n" class="p-4 bg-white rounded-lg shadow-lg">
                <div class="bg-gray-300 h-48 rounded-t-lg"></div>
                <div class="mt-4">
                    <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                    <div class="h-4 bg-gray-300 rounded w-1/4 mt-2"></div>
                </div>
                <div class="mt-4">
                    <div class="h-3 bg-gray-300 rounded w-full"></div>
                    <div class="h-3 bg-gray-300 rounded w-5/6 mt-2"></div>
                    <div class="h-3 bg-gray-300 rounded w-2/3 mt-2"></div>
                </div>
            </div>
        </div>
        <div v-else>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <VideoComponent 
                    v-for="video in videosPaginados" 
                    :description="video.descripcion" 
                    :duration="video.duracion" 
                    :videoSrc="`https://www.youtube.com/watch?v=${video.idVideo}`" 
                />
            </div>
            <div class="flex justify-center mt-4">
                <button 
                    @click="paginaAnterior" 
                    :disabled="paginaActual === 1" 
                    class="btn btn-primary mx-2"
                >
                    Anterior
                </button>
                <button 
                    v-for="pagina in numerosPaginas" 
                    :key="pagina" 
                    @click="irAPagina(pagina)" 
                    :class="['btn mx-1', { 'btn-active': pagina === paginaActual }]"
                >
                    {{ pagina }}
                </button>
                <button 
                    @click="paginaSiguiente" 
                    :disabled="paginaActual === totalPaginas" 
                    class="btn btn-primary mx-2"
                >
                    Siguiente
                </button>
            </div>
            <p v-if="videos.length === 0" class="text-center text-gray-500 mt-4">No se encontraron videos para este canal.</p>
            <p v-if="mensajeError" class="text-center text-red-500 mt-4">{{ mensajeError }}</p>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import VideoComponent from '../components/Podcast/Video.vue';

const videos = ref([]);
const mensajeError = ref('');
const cargando = ref(true); // Nuevo estado para controlar el skeleton loader
const idCanal = 'UC9D4KMFefAZj1ra3h-BX2fg'; // ID del canal proporcionado
const paginaActual = ref(1);
const videosPorPagina = 12; // Cambiado de 9 a 12

// Función para convertir la duración ISO 8601 a segundos
const convertirDuracion = (duracion) => {
    const match = duracion.match(/PT(\d+H)?(\d+M)?(\d+S)?/);
    const horas = parseInt(match[1]) || 0;
    const minutos = parseInt(match[2]) || 0;
    const segundos = parseInt(match[3]) || 0;
    return horas * 3600 + minutos * 60 + segundos;
};

// Función para obtener todos los videos del canal de YouTube
const obtenerVideos = async (idCanal) => {
    const claveApi = 'AIzaSyAf-ou_chtCIqypaE3fBk5LFyh3twFUnHo'; // Reemplaza con tu clave de API de YouTube
    let tokenPagina = '';
    const todosLosVideos = [];

    try {
        do {
            const urlBusqueda = `https://www.googleapis.com/youtube/v3/search?key=${claveApi}&channelId=${idCanal}&part=snippet&type=video&order=date&maxResults=50&pageToken=${tokenPagina}`;
            const respuestaBusqueda = await fetch(urlBusqueda);
            if (!respuestaBusqueda.ok) {
                throw new Error(`Error en la solicitud: ${respuestaBusqueda.statusText}`);
            }
            const datosBusqueda = await respuestaBusqueda.json();
            tokenPagina = datosBusqueda.nextPageToken || null;

            const idsVideos = datosBusqueda.items.map(item => item.id.videoId).join(',');
            const urlVideos = `https://www.googleapis.com/youtube/v3/videos?key=${claveApi}&id=${idsVideos}&part=contentDetails,snippet`;
            const respuestaVideos = await fetch(urlVideos);
            if (!respuestaVideos.ok) {
                throw new Error(`Error en la solicitud: ${respuestaVideos.statusText}`);
            }
            const datosVideos = await respuestaVideos.json();
            const videosFiltrados = datosVideos.items
                .filter(item => convertirDuracion(item.contentDetails.duration) > 60) // Filtra videos con duración > 60 segundos
                .map(item => ({
                    titulo: item.snippet.title,
                    descripcion: item.snippet.description,
                    idVideo: item.id,
                    duracion: item.contentDetails.duration, // Duración en formato ISO 8601
                }));
            todosLosVideos.push(...videosFiltrados);
        } while (tokenPagina);

        videos.value = todosLosVideos;
    } catch (error) {
        console.error('Error al obtener los videos de YouTube:', error);
        mensajeError.value = 'Hubo un problema al cargar los videos. Por favor, inténtalo de nuevo más tarde.';
    }
};

// Computed para obtener los videos de la página actual
const videosPaginados = computed(() => {
    const inicio = (paginaActual.value - 1) * videosPorPagina;
    const fin = inicio + videosPorPagina;
    return videos.value.slice(inicio, fin);
});

// Computed para calcular el total de páginas
const totalPaginas = computed(() => Math.ceil(videos.value.length / videosPorPagina));

// Computed para generar los números de las páginas
const numerosPaginas = computed(() => {
    const paginas = [];
    for (let i = 1; i <= totalPaginas.value; i++) {
        paginas.push(i);
    }
    return paginas;
});

// Funciones para cambiar de página
const paginaSiguiente = () => {
    if (paginaActual.value < totalPaginas.value) {
        paginaActual.value++;
        window.scrollTo({ top: 0, behavior: 'smooth' }); // Desplaza la vista al principio
    }
};

const paginaAnterior = () => {
    if (paginaActual.value > 1) {
        paginaActual.value--;
        window.scrollTo({ top: 0, behavior: 'smooth' }); // Desplaza la vista al principio
    }
};

const irAPagina = (pagina) => {
    paginaActual.value = pagina;
    window.scrollTo({ top: 0, behavior: 'smooth' }); // Desplaza la vista al principio
};

onMounted(async () => {
    cargando.value = true; // Activa el skeleton loader
    await obtenerVideos(idCanal);
    cargando.value = false; // Desactiva el skeleton loader
});
</script>

<style scoped>
/* Espaciado uniforme entre las tarjetas */
.grid {
    margin: 1rem;
}
.btn {
    padding: 0.5rem 1rem;
    background-color: #C18F67; /* Botón principal */
    color: #1F1E1E; /* Texto del botón */
    border: 2px solid #825336; /* Borde del botón */
    border-radius: 0.5rem; /* Bordes redondeados */
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}
.btn:hover {
    background-color: #825336; /* Color al pasar el mouse */
    color: #DBE6ED; /* Texto al pasar el mouse */
    transform: scale(1.05); /* Efecto de zoom */
}
.btn:disabled {
    background-color: #DBE6ED; /* Botón deshabilitado */
    color: #B7CDDA; /* Texto deshabilitado */
    cursor: not-allowed;
}
.btn-active {
    background-color: #431605; /* Botón activo */
    color: #DBE6ED; /* Texto del botón activo */
    font-weight: bold;
}
.text-gray-500 {
    color: #B7CDDA; /* Texto gris */
}
.text-red-500 {
    color: #ff0000; /* Texto rojo */
}
.text-center {
    color: #431605; /* Texto centrado */
}
.animate-pulse {
    animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}
</style>